<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Product;
use App\Models\ProductBatch;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\SaleLine;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class BusinessManagementController extends Controller
{
    public function index(): View
    {
        $today = now()->startOfDay();
        $weekStart = now()->startOfWeek();
        $monthStart = now()->startOfMonth();
        $last30 = now()->subDays(30);

        $salesToday = Sale::query()->where('status', 'completed')->where('completed_at', '>=', $today);
        $salesWeek = Sale::query()->where('status', 'completed')->where('completed_at', '>=', $weekStart);
        $salesMonth = Sale::query()->where('status', 'completed')->where('completed_at', '>=', $monthStart);

        $byBranch = Sale::query()
            ->where('status', 'completed')
            ->where('completed_at', '>=', $last30)
            ->selectRaw('branch_id, COUNT(*) as sale_count, COALESCE(SUM(grand_total),0) as revenue')
            ->groupBy('branch_id')
            ->orderByDesc('revenue')
            ->get();

        $branchNames = Branch::query()->pluck('name', 'id');

        $openPurchases = Purchase::query()
            ->whereNotIn('status', ['received', 'cancelled'])
            ->count();

        $draftSales = Sale::query()->where('status', '!=', 'completed')->count();

        $stockSub = '(SELECT COALESCE(SUM(pb.quantity_on_hand), 0) FROM product_batches pb WHERE pb.product_id = products.id)';

        $lowStock = Product::query()
            ->select('products.id', 'products.name', 'products.internal_code', 'products.min_stock')
            ->selectRaw("{$stockSub} as stock_real")
            ->where('products.is_active', true)
            ->where('products.min_stock', '>', 0)
            ->whereRaw("{$stockSub} < products.min_stock")
            ->orderByRaw("{$stockSub} asc")
            ->limit(12)
            ->get();

        $expiringSoon = ProductBatch::query()
            ->where('quantity_on_hand', '>', 0)
            ->whereNotNull('expires_on')
            ->whereBetween('expires_on', [now()->startOfDay(), now()->copy()->addDays(30)->endOfDay()])
            ->with(['product:id,name'])
            ->orderBy('expires_on')
            ->limit(12)
            ->get(['id', 'product_id', 'lot_number', 'expires_on', 'quantity_on_hand', 'warehouse_id']);

        $topProducts = SaleLine::query()
            ->join('sales', 'sales.id', '=', 'sale_lines.sale_id')
            ->where('sales.status', 'completed')
            ->where('sales.completed_at', '>=', $last30)
            ->select('sale_lines.product_id', DB::raw('SUM(sale_lines.line_total) as revenue'), DB::raw('SUM(sale_lines.quantity) as qty_sold'))
            ->groupBy('sale_lines.product_id')
            ->orderByDesc('revenue')
            ->limit(8)
            ->get();

        $productNames = Product::query()
            ->whereIn('id', $topProducts->pluck('product_id')->filter())
            ->pluck('name', 'id');

        $recentSales = Sale::query()
            ->where('status', 'completed')
            ->with(['branch:id,name', 'user:id,name'])
            ->orderByDesc('completed_at')
            ->limit(12)
            ->get(['id', 'sale_number', 'branch_id', 'user_id', 'grand_total', 'completed_at']);

        return view('gestion.index', [
            'stats' => [
                'branches' => Branch::query()->where('is_active', true)->count(),
                'products_active' => Product::query()->where('is_active', true)->count(),
                'users' => User::query()->count(),
                'ventas_hoy' => (clone $salesToday)->count(),
                'total_hoy' => (clone $salesToday)->sum('grand_total'),
                'ventas_semana' => (clone $salesWeek)->count(),
                'total_semana' => (clone $salesWeek)->sum('grand_total'),
                'ventas_mes' => (clone $salesMonth)->count(),
                'total_mes' => (clone $salesMonth)->sum('grand_total'),
                'open_purchases' => $openPurchases,
                'draft_sales' => $draftSales,
            ],
            'byBranch' => $byBranch,
            'branchNames' => $branchNames,
            'lowStock' => $lowStock,
            'expiringSoon' => $expiringSoon,
            'topProducts' => $topProducts,
            'productNames' => $productNames,
            'recentSales' => $recentSales,
        ]);
    }
}
