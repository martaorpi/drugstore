<?php

namespace App\Http\Controllers\Admin;

use App\Models\Product;
use App\Models\ProductBatch;
use App\Models\StockMovement;
use App\Models\User;
use App\Models\Warehouse;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;

class StockMovementCrudController extends CrudController
{
    use CreateOperation;
    use DeleteOperation;
    use ListOperation;
    use ShowOperation;
    use UpdateOperation;

    public function setup(): void
    {
        $this->crud->setModel(StockMovement::class);
        $this->crud->setRoute(config('backpack.base.route_prefix').'/stock-movements');
        $this->crud->setEntityNameStrings('movimiento de stock', 'movimientos de stock');
    }

    protected function setupListOperation(): void
    {
        $this->crud->setTitle('Movimientos de stock');
        $this->crud->addColumns([
            ['name' => 'created_at', 'label' => 'Fecha', 'type' => 'datetime'],
            [
                'name' => 'warehouse', 'label' => 'Depósito', 'type' => 'select',
                'entity' => 'warehouse', 'attribute' => 'name', 'model' => Warehouse::class,
            ],
            [
                'name' => 'product', 'label' => 'Producto', 'type' => 'select',
                'entity' => 'product', 'attribute' => 'name', 'model' => Product::class,
            ],
            ['name' => 'movement_type', 'label' => 'Tipo', 'type' => 'text'],
            ['name' => 'quantity_delta', 'label' => 'Δ cantidad', 'type' => 'number', 'decimals' => 4],
        ]);
    }

    protected function setupShowOperation(): void
    {
        $this->crud->set('show.setFromDb', true);
    }

    protected function setupCreateOperation(): void
    {
        $this->crud->setValidation([
            'warehouse_id' => 'required|exists:warehouses,id',
            'product_id' => 'required|exists:products,id',
            'movement_type' => 'required|string|max:32',
            'quantity_delta' => 'required|numeric',
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
                'name' => 'warehouse_id', 'label' => 'Depósito', 'type' => 'select',
                'entity' => 'warehouse', 'model' => Warehouse::class, 'attribute' => 'name',
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
            [
                'name' => 'movement_type', 'label' => 'Tipo', 'type' => 'select_from_array',
                'options' => [
                    'sale' => 'Venta',
                    'purchase' => 'Compra',
                    'adjustment' => 'Ajuste',
                    'transfer_in' => 'Transferencia entrada',
                    'transfer_out' => 'Transferencia salida',
                ],
            ],
            ['name' => 'quantity_delta', 'label' => 'Variación (+/-)', 'type' => 'number', 'attributes' => ['step' => '0.0001']],
            ['name' => 'reference_type', 'label' => 'Tipo referencia', 'type' => 'text', 'hint' => 'Ej. App\\Models\\Sale'],
            ['name' => 'reference_id', 'label' => 'ID referencia', 'type' => 'number'],
            [
                'name' => 'user_id', 'label' => 'Usuario', 'type' => 'select',
                'entity' => 'user', 'model' => User::class, 'attribute' => 'name',
                'allows_null' => true,
                'default' => backpack_user()?->id,
            ],
            ['name' => 'note', 'label' => 'Nota', 'type' => 'textarea'],
        ]);
    }
}
