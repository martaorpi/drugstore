<?php

namespace App\Http\Controllers\Admin;

use App\Models\Product;
use App\Models\ProductBatch;
use App\Models\Sale;
use App\Models\SaleLine;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;

class SaleLineCrudController extends CrudController
{
    use CreateOperation;
    use DeleteOperation;
    use ListOperation;
    use ShowOperation;
    use UpdateOperation;

    public function setup(): void
    {
        $this->crud->setModel(SaleLine::class);
        $this->crud->setRoute(config('backpack.base.route_prefix').'/sale-lines');
        $this->crud->setEntityNameStrings('línea de venta', 'líneas de venta');
    }

    protected function setupListOperation(): void
    {
        $this->crud->setTitle('Líneas de venta');
        $this->crud->addColumns([
            [
                'name' => 'sale', 'label' => 'Venta', 'type' => 'select',
                'entity' => 'sale', 'attribute' => 'sale_number', 'model' => Sale::class,
            ],
            [
                'name' => 'product', 'label' => 'Producto', 'type' => 'select',
                'entity' => 'product', 'attribute' => 'name', 'model' => Product::class,
            ],
            ['name' => 'quantity', 'label' => 'Cant.', 'type' => 'number', 'decimals' => 2],
            ['name' => 'unit_price', 'label' => 'P. unit.', 'type' => 'number', 'decimals' => 2],
            ['name' => 'line_total', 'label' => 'Total línea', 'type' => 'number', 'decimals' => 2],
        ]);
    }

    protected function setupShowOperation(): void
    {
        $this->crud->set('show.setFromDb', true);
    }

    protected function setupCreateOperation(): void
    {
        $this->crud->setValidation([
            'sale_id' => 'required|exists:sales,id',
            'product_id' => 'required|exists:products,id',
            'product_name_snapshot' => 'required|string|max:255',
            'quantity' => 'required|numeric|min:0.0001',
            'unit_price' => 'required|numeric|min:0',
            'line_total' => 'required|numeric|min:0',
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
                'name' => 'sale_id', 'label' => 'Venta', 'type' => 'select',
                'entity' => 'sale', 'model' => Sale::class, 'attribute' => 'sale_number',
            ],
            [
                'name' => 'product_id', 'label' => 'Producto', 'type' => 'select',
                'entity' => 'product', 'model' => Product::class, 'attribute' => 'name',
            ],
            [
                'name' => 'product_batch_id', 'label' => 'Lote', 'type' => 'select',
                'entity' => 'productBatch', 'model' => ProductBatch::class, 'attribute' => 'lot_number',
                'allows_null' => true,
            ],
            ['name' => 'product_name_snapshot', 'label' => 'Nombre en ticket', 'type' => 'text'],
            ['name' => 'quantity', 'label' => 'Cantidad', 'type' => 'number', 'attributes' => ['step' => '0.0001']],
            ['name' => 'unit_price', 'label' => 'Precio unitario', 'type' => 'number', 'attributes' => ['step' => '0.0001']],
            ['name' => 'discount_amount', 'label' => 'Descuento', 'type' => 'number', 'attributes' => ['step' => '0.01']],
            ['name' => 'tax_rate', 'label' => 'IVA %', 'type' => 'number', 'attributes' => ['step' => '0.01']],
            ['name' => 'line_total', 'label' => 'Total línea', 'type' => 'number', 'attributes' => ['step' => '0.01']],
            ['name' => 'line_number', 'label' => 'N° línea', 'type' => 'number', 'default' => 1],
        ]);
    }
}
