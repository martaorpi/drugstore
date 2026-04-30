<?php

namespace App\Http\Controllers\Admin;

use App\Models\Branch;
use App\Models\Purchase;
use App\Models\Supplier;
use App\Models\User;
use App\Models\Warehouse;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;

class PurchaseCrudController extends CrudController
{
    use CreateOperation;
    use DeleteOperation;
    use ListOperation;
    use ShowOperation;
    use UpdateOperation;

    public function setup(): void
    {
        $this->crud->setModel(Purchase::class);
        $this->crud->setRoute(config('backpack.base.route_prefix').'/purchases');
        $this->crud->setEntityNameStrings('compra', 'compras');
    }

    protected function setupListOperation(): void
    {
        $this->crud->setTitle('Compras');
        $this->crud->addColumns([
            ['name' => 'purchase_number', 'label' => 'Número', 'type' => 'text'],
            [
                'name' => 'supplier', 'label' => 'Proveedor', 'type' => 'select',
                'entity' => 'supplier', 'attribute' => 'business_name', 'model' => Supplier::class,
            ],
            [
                'name' => 'branch', 'label' => 'Sucursal', 'type' => 'select',
                'entity' => 'branch', 'attribute' => 'name', 'model' => Branch::class,
            ],
            ['name' => 'status', 'label' => 'Estado', 'type' => 'text'],
            ['name' => 'grand_total', 'label' => 'Total', 'type' => 'number', 'decimals' => 2],
        ]);
    }

    protected function setupShowOperation(): void
    {
        $this->crud->set('show.setFromDb', true);
    }

    protected function setupCreateOperation(): void
    {
        $this->crud->setValidation([
            'supplier_id' => 'required|exists:suppliers,id',
            'branch_id' => 'required|exists:branches,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'status' => 'required|string|max:24',
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
            ['name' => 'purchase_number', 'label' => 'Número (vacío = auto)', 'type' => 'text'],
            [
                'name' => 'supplier_id', 'label' => 'Proveedor', 'type' => 'select',
                'entity' => 'supplier', 'model' => Supplier::class, 'attribute' => 'business_name',
            ],
            [
                'name' => 'branch_id', 'label' => 'Sucursal', 'type' => 'select',
                'entity' => 'branch', 'model' => Branch::class, 'attribute' => 'name',
            ],
            [
                'name' => 'warehouse_id', 'label' => 'Depósito', 'type' => 'select',
                'entity' => 'warehouse', 'model' => Warehouse::class, 'attribute' => 'name',
            ],
            [
                'name' => 'user_id', 'label' => 'Usuario', 'type' => 'select',
                'entity' => 'user', 'model' => User::class, 'attribute' => 'name',
                'allows_null' => true,
            ],
            [
                'name' => 'status', 'label' => 'Estado', 'type' => 'select_from_array',
                'options' => [
                    'draft' => 'Borrador',
                    'ordered' => 'Pedida',
                    'partial' => 'Parcial',
                    'received' => 'Recibida',
                    'cancelled' => 'Anulada',
                ],
                'default' => 'draft',
            ],
            ['name' => 'ordered_at', 'label' => 'Fecha pedido', 'type' => 'datetime'],
            ['name' => 'received_at', 'label' => 'Fecha recepción', 'type' => 'datetime'],
            ['name' => 'subtotal', 'label' => 'Subtotal', 'type' => 'number', 'attributes' => ['step' => '0.01']],
            ['name' => 'tax_total', 'label' => 'IVA', 'type' => 'number', 'attributes' => ['step' => '0.01']],
            ['name' => 'grand_total', 'label' => 'Total', 'type' => 'number', 'attributes' => ['step' => '0.01']],
            ['name' => 'supplier_invoice_number', 'label' => 'N° factura proveedor', 'type' => 'text'],
            ['name' => 'notes', 'label' => 'Notas', 'type' => 'textarea'],
        ]);
    }
}
