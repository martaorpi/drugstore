<?php

namespace App\Http\Controllers\Admin;

use App\Models\Product;
use App\Models\ProductBatch;
use App\Models\Purchase;
use App\Models\PurchaseLine;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;

class PurchaseLineCrudController extends CrudController
{
    use CreateOperation;
    use DeleteOperation;
    use ListOperation;
    use ShowOperation;
    use UpdateOperation;

    public function setup(): void
    {
        $this->crud->setModel(PurchaseLine::class);
        $this->crud->setRoute(config('backpack.base.route_prefix').'/purchase-lines');
        $this->crud->setEntityNameStrings('línea de compra', 'líneas de compra');
    }

    protected function setupListOperation(): void
    {
        $this->crud->setTitle('Líneas de compra');
        $this->crud->addColumns([
            [
                'name' => 'purchase', 'label' => 'Compra', 'type' => 'select',
                'entity' => 'purchase', 'attribute' => 'purchase_number', 'model' => Purchase::class,
            ],
            [
                'name' => 'product', 'label' => 'Producto', 'type' => 'select',
                'entity' => 'product', 'attribute' => 'name', 'model' => Product::class,
            ],
            ['name' => 'quantity_ordered', 'label' => 'Pedido', 'type' => 'number', 'decimals' => 2],
            ['name' => 'quantity_received', 'label' => 'Recibido', 'type' => 'number', 'decimals' => 2],
            ['name' => 'unit_cost', 'label' => 'Costo', 'type' => 'number', 'decimals' => 2],
        ]);
    }

    protected function setupShowOperation(): void
    {
        $this->crud->set('show.setFromDb', true);
    }

    protected function setupCreateOperation(): void
    {
        $this->crud->setValidation([
            'purchase_id' => 'required|exists:purchases,id',
            'product_id' => 'required|exists:products,id',
            'quantity_ordered' => 'required|numeric|min:0',
            'unit_cost' => 'required|numeric|min:0',
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
                'name' => 'purchase_id', 'label' => 'Compra', 'type' => 'select',
                'entity' => 'purchase', 'model' => Purchase::class, 'attribute' => 'purchase_number',
            ],
            [
                'name' => 'product_id', 'label' => 'Producto', 'type' => 'select',
                'entity' => 'product', 'model' => Product::class, 'attribute' => 'name',
            ],
            [
                'name' => 'product_batch_id', 'label' => 'Lote asociado', 'type' => 'select',
                'entity' => 'productBatch', 'model' => ProductBatch::class, 'attribute' => 'lot_number',
                'allows_null' => true,
            ],
            ['name' => 'quantity_ordered', 'label' => 'Cantidad pedida', 'type' => 'number', 'attributes' => ['step' => '0.0001']],
            ['name' => 'quantity_received', 'label' => 'Cantidad recibida', 'type' => 'number', 'attributes' => ['step' => '0.0001']],
            ['name' => 'unit_cost', 'label' => 'Costo unitario', 'type' => 'number', 'attributes' => ['step' => '0.0001']],
            ['name' => 'lot_number', 'label' => 'N° lote (texto)', 'type' => 'text'],
            ['name' => 'expires_on', 'label' => 'Vencimiento', 'type' => 'date'],
            ['name' => 'line_number', 'label' => 'N° línea', 'type' => 'number', 'default' => 1],
        ]);
    }
}
