<?php

use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => config('backpack.base.route_prefix', 'admin'),
    'middleware' => array_merge(
        (array) config('backpack.base.web_middleware', 'web'),
        (array) config('backpack.base.middleware_key', 'admin')
    ),
    'namespace' => 'App\Http\Controllers\Admin',
], function () {
    Route::crud('branches', 'BranchCrudController');
    Route::crud('warehouses', 'WarehouseCrudController');
    Route::crud('categories', 'CategoryCrudController');
    Route::crud('brands', 'BrandCrudController');
    Route::crud('suppliers', 'SupplierCrudController');
    Route::crud('customers', 'CustomerCrudController');
    Route::crud('price-lists', 'PriceListCrudController');
    Route::crud('products', 'ProductCrudController');
    Route::crud('product-prices', 'ProductPriceCrudController');
    Route::crud('product-batches', 'ProductBatchCrudController');
    Route::crud('payment-methods', 'PaymentMethodCrudController');
    Route::crud('cash-registers', 'CashRegisterCrudController');
    Route::crud('cash-sessions', 'CashSessionCrudController');
    Route::crud('sales', 'SaleCrudController');
    Route::crud('sale-lines', 'SaleLineCrudController');
    Route::crud('sale-payments', 'SalePaymentCrudController');
    Route::crud('purchases', 'PurchaseCrudController');
    Route::crud('purchase-lines', 'PurchaseLineCrudController');
    Route::crud('stock-movements', 'StockMovementCrudController');
    Route::crud('users', 'UserCrudController');
});
