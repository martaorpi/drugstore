<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Brand;
use App\Models\CashRegister;
use App\Models\Category;
use App\Models\Customer;
use App\Models\PaymentMethod;
use App\Models\PriceList;
use App\Models\Product;
use App\Models\ProductBatch;
use App\Models\ProductPrice;
use App\Models\Purchase;
use App\Models\PurchaseLine;
use App\Models\Sale;
use App\Models\SaleLine;
use App\Models\SalePayment;
use App\Models\Supplier;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DrugstoreDemoSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            $branch = Branch::updateOrCreate(
                ['code' => 'MAIN'],
                [
                    'name' => 'Sucursal Centro',
                    'address' => 'Av. Corrientes 1234',
                    'city' => 'CABA',
                    'province' => 'CABA',
                    'postal_code' => 'C1043',
                    'phone' => '11-5555-1000',
                    'email' => 'centro@minimarket.local',
                    'tax_id' => '30-70000000-1',
                    'is_active' => true,
                ]
            );

            $warehouse = Warehouse::updateOrCreate(
                ['branch_id' => $branch->id, 'code' => 'DEP'],
                [
                    'name' => 'Depósito principal',
                    'is_default' => true,
                    'is_active' => true,
                ]
            );

            $catBebidas = Category::updateOrCreate(
                ['code' => 'DEMO-BEB'],
                ['name' => 'Bebidas', 'sort_order' => 10, 'is_active' => true]
            );
            $catAlmacen = Category::updateOrCreate(
                ['code' => 'DEMO-ALM'],
                ['name' => 'Almacén', 'sort_order' => 20, 'is_active' => true]
            );
            $catLacteos = Category::updateOrCreate(
                ['code' => 'DEMO-LAC'],
                ['name' => 'Lácteos', 'sort_order' => 30, 'is_active' => true]
            );

            $brandCoca = Brand::updateOrCreate(['name' => 'Coca-Cola'], ['is_active' => true]);
            $brandArcor = Brand::updateOrCreate(['name' => 'Arcor'], ['is_active' => true]);
            $brandSerenisima = Brand::updateOrCreate(['name' => 'La Serenísima'], ['is_active' => true]);
            $brandGenerico = Brand::updateOrCreate(['name' => 'Genérico'], ['is_active' => true]);

            $supplierMayorista = Supplier::updateOrCreate(
                ['code' => 'DEMO-MAY'],
                [
                    'business_name' => 'Mayorista Sur S.A.',
                    'tax_id' => '30-61000000-7',
                    'contact_name' => 'Laura Méndez',
                    'phone' => '11-4000-2000',
                    'email' => 'ventas@mayoristasur.demo',
                    'address' => 'Ruta 3 Km 32',
                    'city' => 'Quilmes',
                    'notes' => 'Proveedor demo',
                    'is_active' => true,
                ]
            );
            $supplierLacteos = Supplier::updateOrCreate(
                ['code' => 'DEMO-LAC'],
                [
                    'business_name' => 'Distribuidora Lácteos Norte',
                    'tax_id' => '30-62000000-3',
                    'contact_name' => 'Carlos Pérez',
                    'phone' => '11-4000-3000',
                    'email' => 'logistica@lacteosnorte.demo',
                    'address' => 'Panamericana Km 45',
                    'city' => 'Pilar',
                    'is_active' => true,
                ]
            );

            $priceList = PriceList::updateOrCreate(
                ['branch_id' => $branch->id, 'code' => 'VTA'],
                [
                    'name' => 'Lista mostrador',
                    'is_default' => true,
                    'is_active' => true,
                ]
            );

            $productDefs = [
                [
                    'internal_code' => 'DEMO-001',
                    'barcode' => '7790310981234',
                    'name' => 'Yerba mate 500 g',
                    'category_id' => $catAlmacen->id,
                    'brand_id' => $brandGenerico->id,
                    'supplier_id' => $supplierMayorista->id,
                    'cost_average' => 900,
                    'last_purchase_cost' => 920,
                    'tax_rate' => 21,
                    'min_stock' => 20,
                    'track_batches' => false,
                    'list_price' => 1500,
                    'lot' => 'SIN-LOTE',
                    'qty' => 80,
                ],
                [
                    'internal_code' => 'DEMO-002',
                    'barcode' => '7790890645123',
                    'name' => 'Gaseosa cola 2,25 L',
                    'category_id' => $catBebidas->id,
                    'brand_id' => $brandCoca->id,
                    'supplier_id' => $supplierMayorista->id,
                    'cost_average' => 1100,
                    'last_purchase_cost' => 1120,
                    'tax_rate' => 21,
                    'min_stock' => 12,
                    'track_batches' => true,
                    'list_price' => 2200,
                    'lot' => 'L2026-A01',
                    'qty' => 36,
                    'expires_on' => now()->addMonths(8)->toDateString(),
                ],
                [
                    'internal_code' => 'DEMO-003',
                    'barcode' => '7790742296001',
                    'name' => 'Leche entera sachet 1 L',
                    'category_id' => $catLacteos->id,
                    'brand_id' => $brandSerenisima->id,
                    'supplier_id' => $supplierLacteos->id,
                    'cost_average' => 650,
                    'last_purchase_cost' => 660,
                    'tax_rate' => 10.5,
                    'min_stock' => 24,
                    'track_batches' => true,
                    'list_price' => 980,
                    'lot' => 'LAC-2404',
                    'qty' => 48,
                    'expires_on' => now()->addWeeks(3)->toDateString(),
                ],
                [
                    'internal_code' => 'DEMO-004',
                    'barcode' => '7790580498123',
                    'name' => 'Alfajor triple chocolate',
                    'category_id' => $catAlmacen->id,
                    'brand_id' => $brandArcor->id,
                    'supplier_id' => $supplierMayorista->id,
                    'cost_average' => 280,
                    'last_purchase_cost' => 290,
                    'tax_rate' => 21,
                    'min_stock' => 30,
                    'track_batches' => false,
                    'list_price' => 450,
                    'lot' => 'SIN-LOTE',
                    'qty' => 120,
                ],
                [
                    'internal_code' => 'DEMO-005',
                    'barcode' => '7790310456789',
                    'name' => 'Agua mineral 2 L',
                    'category_id' => $catBebidas->id,
                    'brand_id' => $brandGenerico->id,
                    'supplier_id' => $supplierMayorista->id,
                    'cost_average' => 320,
                    'last_purchase_cost' => 330,
                    'tax_rate' => 21,
                    'min_stock' => 18,
                    'track_batches' => true,
                    'list_price' => 550,
                    'lot' => 'AG-2026-02',
                    'qty' => 60,
                    'expires_on' => now()->addYear()->toDateString(),
                ],
            ];

            $products = [];
            foreach ($productDefs as $def) {
                $listPrice = $def['list_price'];
                $supplierId = $def['supplier_id'];
                unset($def['list_price'], $def['supplier_id'], $def['lot'], $def['qty'], $def['expires_on']);

                $product = Product::updateOrCreate(
                    ['internal_code' => $def['internal_code']],
                    array_merge($def, [
                        'sale_unit' => 'unidad',
                        'preferred_supplier_id' => $supplierId,
                        'is_active' => true,
                    ])
                );
                $products[] = $product;

                ProductPrice::updateOrCreate(
                    ['product_id' => $product->id, 'price_list_id' => $priceList->id],
                    ['price' => $listPrice, 'valid_from' => null, 'valid_until' => null]
                );
            }

            foreach ($productDefs as $i => $orig) {
                $product = Product::where('internal_code', $orig['internal_code'])->first();
                ProductBatch::updateOrCreate(
                    [
                        'warehouse_id' => $warehouse->id,
                        'product_id' => $product->id,
                        'lot_number' => $orig['lot'],
                    ],
                    [
                        'expires_on' => $orig['expires_on'] ?? null,
                        'quantity_on_hand' => $orig['qty'],
                        'unit_cost' => $product->last_purchase_cost ?? $product->cost_average,
                        'supplier_id' => $orig['supplier_id'] ?? null,
                    ]
                );
            }

            Customer::updateOrCreate(
                ['code' => 'DEMO-CF'],
                [
                    'display_name' => 'Consumidor final',
                    'tax_condition' => 'Consumidor final',
                    'is_active' => true,
                ]
            );
            Customer::updateOrCreate(
                ['code' => 'DEMO-RI'],
                [
                    'display_name' => 'Kiosco Don Pepe',
                    'tax_id' => '20-30111222-3',
                    'tax_condition' => 'Responsable inscripto',
                    'email' => 'pepe@kiosco.demo',
                    'phone' => '11-6666-7788',
                    'address' => 'Av. Rivadavia 8900',
                    'city' => 'CABA',
                    'credit_limit' => 50000,
                    'is_active' => true,
                ]
            );

            $cashRegister = CashRegister::updateOrCreate(
                ['branch_id' => $branch->id, 'code' => 'CAJA1'],
                [
                    'name' => 'Caja 1',
                    'default_warehouse_id' => $warehouse->id,
                    'is_active' => true,
                ]
            );

            $admin = User::query()->where('email', 'admin@admin')->first();
            if ($admin) {
                $admin->update(['default_branch_id' => $branch->id]);
            }

            $this->seedDemoSale($branch, $warehouse, $cashRegister, $priceList, $products);
            $this->seedDemoPurchase($branch, $warehouse, $supplierMayorista, $admin?->id);
        });
    }

    /**
     * @param  array<int, Product>  $products
     */
    private function seedDemoSale(
        Branch $branch,
        Warehouse $warehouse,
        CashRegister $cashRegister,
        PriceList $priceList,
        array $products
    ): void {
        if (Sale::query()->where('sale_number', 'DEMO-SALE-001')->exists()) {
            return;
        }

        $admin = User::query()->where('email', 'admin@admin')->first();
        if (! $admin) {
            return;
        }

        $cashMethod = PaymentMethod::query()->where('code', 'cash')->first();
        if (! $cashMethod) {
            return;
        }

        $p1 = $products[0] ?? null;
        $p2 = $products[3] ?? null;
        if (! $p1 || ! $p2) {
            return;
        }

        $batch1 = ProductBatch::query()
            ->where('product_id', $p1->id)
            ->where('warehouse_id', $warehouse->id)
            ->first();
        $batch2 = ProductBatch::query()
            ->where('product_id', $p2->id)
            ->where('warehouse_id', $warehouse->id)
            ->first();

        $line1Net = round(2 * 1500, 2);
        $line1Tax = round($line1Net * ($p1->tax_rate / 100), 2);
        $line1Total = round($line1Net + $line1Tax, 2);

        $line2Net = round(5 * 450, 2);
        $line2Tax = round($line2Net * ($p2->tax_rate / 100), 2);
        $line2Total = round($line2Net + $line2Tax, 2);

        $subtotalEx = round($line1Net + $line2Net, 2);
        $taxTotal = round($line1Tax + $line2Tax, 2);
        $grand = round($subtotalEx + $taxTotal, 2);

        $sale = Sale::create([
            'sale_number' => 'DEMO-SALE-001',
            'branch_id' => $branch->id,
            'warehouse_id' => $warehouse->id,
            'cash_register_id' => $cashRegister->id,
            'cash_session_id' => null,
            'user_id' => $admin->id,
            'customer_id' => null,
            'price_list_id' => $priceList->id,
            'channel' => 'pos',
            'status' => 'completed',
            'subtotal_ex_tax' => $subtotalEx,
            'tax_total' => $taxTotal,
            'discount_total' => 0,
            'rounding' => 0,
            'grand_total' => $grand,
            'completed_at' => now()->subDay(),
            'notes' => 'Venta de ejemplo (seeder)',
        ]);

        SaleLine::create([
            'sale_id' => $sale->id,
            'product_id' => $p1->id,
            'product_batch_id' => $batch1?->id,
            'product_name_snapshot' => $p1->name,
            'quantity' => 2,
            'unit_price' => 1500,
            'discount_amount' => 0,
            'tax_rate' => $p1->tax_rate,
            'line_total' => $line1Total,
            'line_number' => 1,
        ]);

        SaleLine::create([
            'sale_id' => $sale->id,
            'product_id' => $p2->id,
            'product_batch_id' => $batch2?->id,
            'product_name_snapshot' => $p2->name,
            'quantity' => 5,
            'unit_price' => 450,
            'discount_amount' => 0,
            'tax_rate' => $p2->tax_rate,
            'line_total' => $line2Total,
            'line_number' => 2,
        ]);

        SalePayment::create([
            'sale_id' => $sale->id,
            'payment_method_id' => $cashMethod->id,
            'amount' => $grand,
            'reference' => null,
        ]);
    }

    private function seedDemoPurchase(Branch $branch, Warehouse $warehouse, Supplier $supplier, ?int $userId): void
    {
        if (Purchase::query()->where('purchase_number', 'DEMO-PO-001')->exists()) {
            return;
        }

        $yerba = Product::query()->where('internal_code', 'DEMO-001')->first();
        if (! $yerba) {
            return;
        }

        $qty = 24;
        $unit = 880;
        $net = round($qty * $unit, 2);
        $tax = round($net * 0.21, 2);
        $grand = round($net + $tax, 2);

        $purchase = Purchase::create([
            'purchase_number' => 'DEMO-PO-001',
            'supplier_id' => $supplier->id,
            'branch_id' => $branch->id,
            'warehouse_id' => $warehouse->id,
            'status' => 'received',
            'ordered_at' => now()->subDays(3),
            'received_at' => now()->subDays(2),
            'subtotal' => $net,
            'tax_total' => $tax,
            'grand_total' => $grand,
            'supplier_invoice_number' => 'A-0001-00001234',
            'notes' => 'Compra de ejemplo (seeder)',
            'user_id' => $userId,
        ]);

        PurchaseLine::create([
            'purchase_id' => $purchase->id,
            'product_id' => $yerba->id,
            'product_batch_id' => null,
            'quantity_ordered' => $qty,
            'quantity_received' => $qty,
            'unit_cost' => $unit,
            'lot_number' => 'OC-DEMO-01',
            'expires_on' => null,
            'line_number' => 1,
        ]);
    }
}
