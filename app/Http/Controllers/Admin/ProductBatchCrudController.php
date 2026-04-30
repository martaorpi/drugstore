<?php

namespace App\Http\Controllers\Admin;

use App\Models\Product;
use App\Models\ProductBatch;
use App\Models\Supplier;
use App\Models\Warehouse;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;

class ProductBatchCrudController extends CrudController
{
    use CreateOperation;
    use DeleteOperation;
    use ListOperation;
    use ShowOperation;
    use UpdateOperation;

    public function setup(): void
    {
        $this->crud->setModel(ProductBatch::class);
        $this->crud->setRoute(config('backpack.base.route_prefix').'/product-batches');
        $this->crud->setEntityNameStrings('lote', 'lotes');
    }

    protected function setupListOperation(): void
    {
        $this->crud->setTitle('Lotes y vencimientos');
        $this->crud->addColumns([
            [
                'name' => 'product', 'label' => 'Producto', 'type' => 'select',
                'entity' => 'product', 'attribute' => 'name', 'model' => Product::class,
            ],
            [
                'name' => 'warehouse', 'label' => 'Depósito', 'type' => 'select',
                'entity' => 'warehouse', 'attribute' => 'name', 'model' => Warehouse::class,
            ],
            ['name' => 'lot_number', 'label' => 'Lote', 'type' => 'text'],
            ['name' => 'expires_on', 'label' => 'Vencimiento', 'type' => 'date'],
            ['name' => 'quantity_on_hand', 'label' => 'Cantidad', 'type' => 'number', 'decimals' => 2],
        ]);
    }

    protected function setupShowOperation(): void
    {
        $this->crud->set('show.setFromDb', true);
    }

    protected function setupCreateOperation(): void
    {
        $this->crud->setValidation([
            'product_id' => 'required|exists:products,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'lot_number' => 'required|string|max:64',
            'quantity_on_hand' => 'required|numeric|min:0',
        ]);
        $this->addFields();
    }

    protected function setupUpdateOperation(): void
    {
        $this->setupCreateOperation();
    }

    private function addFields(): void
    {
        $this->crud->addFields([
            [
                'name' => 'product_id', 'label' => 'Producto', 'type' => 'select',
                'entity' => 'product', 'model' => Product::class, 'attribute' => 'name',
            ],
            [
                'name' => 'warehouse_id', 'label' => 'Depósito', 'type' => 'select',
                'entity' => 'warehouse', 'model' => Warehouse::class, 'attribute' => 'name',
            ],
            ['name' => 'lot_number', 'label' => 'Número de lote', 'type' => 'text'],
            ['name' => 'expires_on', 'label' => 'Vencimiento', 'type' => 'date'],
            ['name' => 'quantity_on_hand', 'label' => 'Cantidad disponible', 'type' => 'number', 'attributes' => ['step' => '0.0001']],
            ['name' => 'unit_cost', 'label' => 'Costo unitario', 'type' => 'number', 'attributes' => ['step' => '0.0001']],
            [
                'name' => 'supplier_id', 'label' => 'Proveedor', 'type' => 'select',
                'entity' => 'supplier', 'model' => Supplier::class, 'attribute' => 'business_name',
                'allows_null' => true,
            ],
        ]);
    }
}
