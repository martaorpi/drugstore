<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\PaymentMethod;
use App\Models\PriceList;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleLine;
use App\Models\SalePayment;
use App\Models\Warehouse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class StorePanelController extends Controller
{
    public function index(): View
    {
        $ctx = $this->resolveContext(backpack_user());

        $today = now()->startOfDay();
        $salesToday = Sale::query()
            ->when($ctx['branch_id'], fn ($q, $id) => $q->where('branch_id', $id))
            ->where('status', 'completed')
            ->where('completed_at', '>=', $today)
            ->orderByDesc('completed_at');

        $stats = [
            'branch' => $ctx['branch_id'] ? Branch::find($ctx['branch_id'])?->name : null,
            'ventas_hoy' => (clone $salesToday)->count(),
            'total_hoy' => (clone $salesToday)->sum('grand_total'),
            'ultimas' => Sale::query()
                ->when($ctx['branch_id'], fn ($q, $id) => $q->where('branch_id', $id))
                ->where('status', 'completed')
                ->orderByDesc('completed_at')
                ->limit(8)
                ->get(['id', 'sale_number', 'grand_total', 'completed_at']),
        ];

        return view('store-panel.index', ['stats' => $stats, 'ctx' => $ctx]);
    }

    public function pos(): View
    {
        return view('store-panel.pos', [
            'ctx' => $this->resolveContext(backpack_user()),
        ]);
    }

    public function searchProducts(Request $request): JsonResponse
    {
        $q = trim((string) $request->query('q', ''));
        if (strlen($q) < 1) {
            return response()->json(['data' => []]);
        }

        $ctx = $this->resolveContext(backpack_user());
        $priceListId = $ctx['price_list_id'];

        $products = Product::query()
            ->where('is_active', true)
            ->where(function ($query) use ($q) {
                $query->where('internal_code', $q)
                    ->orWhere('barcode', $q)
                    ->orWhere('name', 'like', '%'.$q.'%');
            })
            ->orderByRaw('CASE WHEN barcode = ? OR internal_code = ? THEN 0 ELSE 1 END', [$q, $q])
            ->limit(24)
            ->get(['id', 'name', 'internal_code', 'barcode', 'tax_rate', 'sale_unit']);

        $data = $products->map(function (Product $p) use ($priceListId) {
            return [
                'id' => $p->id,
                'name' => $p->name,
                'internal_code' => $p->internal_code,
                'barcode' => $p->barcode,
                'sale_unit' => $p->sale_unit,
                'tax_rate' => (float) $p->tax_rate,
                'unit_price' => $this->suggestedUnitPrice($p, $priceListId),
            ];
        });

        return response()->json(['data' => $data]);
    }

    public function storeSale(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'lines' => ['required', 'array', 'min:1'],
            'lines.*.product_id' => ['required', 'exists:products,id'],
            'lines.*.quantity' => ['required', 'numeric', 'min:0.0001'],
            'lines.*.unit_price' => ['required', 'numeric', 'min:0'],
        ]);

        $user = backpack_user();
        $ctx = $this->resolveContext($user);

        if (! $ctx['branch_id']) {
            return response()->json(['message' => 'Configurá al menos una sucursal en el administrador.'], 422);
        }

        $cash = PaymentMethod::query()->where('code', 'cash')->where('is_active', true)->first();
        if (! $cash) {
            return response()->json(['message' => 'No hay medio de pago «Efectivo». Ejecutá el seeder o crealo en el admin.'], 422);
        }

        $sale = null;

        try {
            DB::transaction(function () use ($validated, $user, $ctx, $cash, &$sale) {
                $subtotalEx = 0.0;
                $taxTotal = 0.0;
                $grand = 0.0;
                $lineNumber = 1;

                $sale = Sale::create([
                    'branch_id' => $ctx['branch_id'],
                    'warehouse_id' => $ctx['warehouse_id'],
                    'user_id' => $user->id,
                    'customer_id' => null,
                    'price_list_id' => $ctx['price_list_id'],
                    'channel' => 'pos',
                    'status' => 'completed',
                    'subtotal_ex_tax' => 0,
                    'tax_total' => 0,
                    'discount_total' => 0,
                    'rounding' => 0,
                    'grand_total' => 0,
                    'completed_at' => now(),
                ]);

                foreach ($validated['lines'] as $line) {
                    $product = Product::findOrFail($line['product_id']);
                    $qty = (float) $line['quantity'];
                    $unit = (float) $line['unit_price'];
                    $rate = (float) $product->tax_rate;
                    $grossLine = round($qty * $unit, 2);
                    $netLine = $rate > 0
                        ? round($grossLine / (1 + $rate / 100), 2)
                        : $grossLine;
                    $taxLine = round($grossLine - $netLine, 2);

                    SaleLine::create([
                        'sale_id' => $sale->id,
                        'product_id' => $product->id,
                        'product_batch_id' => null,
                        'product_name_snapshot' => $product->name,
                        'quantity' => $qty,
                        'unit_price' => $unit,
                        'discount_amount' => 0,
                        'tax_rate' => $rate,
                        'line_total' => $grossLine,
                        'line_number' => $lineNumber++,
                    ]);

                    $subtotalEx += $netLine;
                    $taxTotal += $taxLine;
                    $grand += $grossLine;
                }

                $sale->update([
                    'subtotal_ex_tax' => round($subtotalEx, 2),
                    'tax_total' => round($taxTotal, 2),
                    'grand_total' => round($grand, 2),
                ]);

                SalePayment::create([
                    'sale_id' => $sale->id,
                    'payment_method_id' => $cash->id,
                    'amount' => round($grand, 2),
                    'reference' => null,
                ]);
            });
        } catch (\Throwable $e) {
            report($e);

            return response()->json(['message' => 'No se pudo registrar la venta. Revisá los datos e intentá de nuevo.'], 500);
        }

        return response()->json([
            'message' => 'Venta registrada.',
            'sale' => [
                'id' => $sale->id,
                'sale_number' => $sale->sale_number,
                'grand_total' => (float) $sale->grand_total,
            ],
        ], 201);
    }

    /**
     * @return array{branch_id: ?int, warehouse_id: ?int, price_list_id: ?int}
     */
    private function resolveContext($user): array
    {
        $branchId = $user->default_branch_id;

        if (! $branchId) {
            $branchId = Branch::query()->where('is_active', true)->orderBy('id')->value('id');
        }

        $warehouseId = null;
        if ($branchId) {
            $warehouseId = Warehouse::query()
                ->where('branch_id', $branchId)
                ->where('is_active', true)
                ->orderByDesc('is_default')
                ->orderBy('id')
                ->value('id');
        }

        $priceListId = null;
        if ($branchId) {
            $priceListId = PriceList::query()
                ->where('is_active', true)
                ->where(function ($q) use ($branchId) {
                    $q->whereNull('branch_id')->orWhere('branch_id', $branchId);
                })
                ->orderByRaw('CASE WHEN branch_id = ? THEN 0 ELSE 1 END', [$branchId])
                ->orderByDesc('is_default')
                ->orderBy('id')
                ->value('id');
        }

        return [
            'branch_id' => $branchId ? (int) $branchId : null,
            'warehouse_id' => $warehouseId ? (int) $warehouseId : null,
            'price_list_id' => $priceListId ? (int) $priceListId : null,
        ];
    }

    private function suggestedUnitPrice(Product $product, ?int $priceListId): float
    {
        $relation = $product->productPrices();
        if ($priceListId) {
            $relation->where('price_list_id', $priceListId);
        }

        $first = $relation->orderByDesc('id')->first();
        if ($first) {
            return (float) $first->price;
        }

        $any = $product->productPrices()->orderByDesc('id')->first();

        return $any ? (float) $any->price : 0.0;
    }
}
