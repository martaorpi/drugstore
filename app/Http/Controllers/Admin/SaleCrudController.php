<?php

namespace App\Http\Controllers\Admin;

use App\Models\Branch;
use App\Models\CashRegister;
use App\Models\CashSession;
use App\Models\Customer;
use App\Models\PriceList;
use App\Models\Sale;
use App\Models\User;
use App\Models\Warehouse;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;

class SaleCrudController extends CrudController
{
    use CreateOperation;
    use DeleteOperation;
    use ListOperation;
    use ShowOperation;
    use UpdateOperation;

    public function setup(): void
    {
        $this->crud->setModel(Sale::class);
        $this->crud->setRoute(config('backpack.base.route_prefix').'/sales');
        $this->crud->setEntityNameStrings('venta', 'ventas');
    }

    protected function setupListOperation(): void
    {
        $this->crud->setTitle('Ventas');
        $this->crud->addColumns([
            ['name' => 'sale_number', 'label' => 'Número', 'type' => 'text'],
            [
                'name' => 'branch', 'label' => 'Sucursal', 'type' => 'select',
                'entity' => 'branch', 'attribute' => 'name', 'model' => Branch::class,
            ],
            [
                'name' => 'customer', 'label' => 'Cliente', 'type' => 'select',
                'entity' => 'customer', 'attribute' => 'display_name', 'model' => Customer::class,
            ],
            ['name' => 'status', 'label' => 'Estado', 'type' => 'text'],
            ['name' => 'grand_total', 'label' => 'Total', 'type' => 'number', 'decimals' => 2],
            ['name' => 'created_at', 'label' => 'Fecha', 'type' => 'datetime'],
        ]);
    }

    protected function setupShowOperation(): void
    {
        $this->crud->set('show.setFromDb', true);
    }

    protected function setupCreateOperation(): void
    {
        $this->crud->setValidation([
            'branch_id' => 'required|exists:branches,id',
            'user_id' => 'required|exists:users,id',
            'status' => 'required|string|max:24',
            'grand_total' => 'nullable|numeric',
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
            ['name' => 'sale_number', 'label' => 'Número (vacío = auto)', 'type' => 'text', 'hint' => 'Dejá en blanco para generar automáticamente.'],
            [
                'name' => 'branch_id', 'label' => 'Sucursal', 'type' => 'select',
                'entity' => 'branch', 'model' => Branch::class, 'attribute' => 'name',
            ],
            [
                'name' => 'warehouse_id', 'label' => 'Depósito', 'type' => 'select',
                'entity' => 'warehouse', 'model' => Warehouse::class, 'attribute' => 'name',
                'allows_null' => true,
            ],
            [
                'name' => 'cash_register_id', 'label' => 'Caja', 'type' => 'select',
                'entity' => 'cashRegister', 'model' => CashRegister::class, 'attribute' => 'name',
                'allows_null' => true,
            ],
            [
                'name' => 'cash_session_id', 'label' => 'Sesión de caja', 'type' => 'select',
                'entity' => 'cashSession', 'model' => CashSession::class, 'attribute' => 'id',
                'allows_null' => true,
            ],
            [
                'name' => 'user_id', 'label' => 'Vendedor', 'type' => 'select',
                'entity' => 'user', 'model' => User::class, 'attribute' => 'name',
                'default' => backpack_user()?->id,
            ],
            [
                'name' => 'customer_id', 'label' => 'Cliente', 'type' => 'select',
                'entity' => 'customer', 'model' => Customer::class, 'attribute' => 'display_name',
                'allows_null' => true,
            ],
            [
                'name' => 'price_list_id', 'label' => 'Lista de precios', 'type' => 'select',
                'entity' => 'priceList', 'model' => PriceList::class, 'attribute' => 'name',
                'allows_null' => true,
            ],
            [
                'name' => 'channel', 'label' => 'Canal', 'type' => 'select_from_array',
                'options' => ['pos' => 'POS', 'manual' => 'Manual', 'ecommerce' => 'E-commerce'],
                'default' => 'pos',
            ],
            [
                'name' => 'status', 'label' => 'Estado', 'type' => 'select_from_array',
                'options' => ['draft' => 'Borrador', 'completed' => 'Completada', 'cancelled' => 'Anulada'],
                'default' => 'draft',
            ],
            ['name' => 'subtotal_ex_tax', 'label' => 'Subtotal sin IVA', 'type' => 'number', 'attributes' => ['step' => '0.01']],
            ['name' => 'tax_total', 'label' => 'IVA', 'type' => 'number', 'attributes' => ['step' => '0.01']],
            ['name' => 'discount_total', 'label' => 'Descuentos', 'type' => 'number', 'attributes' => ['step' => '0.01']],
            ['name' => 'rounding', 'label' => 'Redondeo', 'type' => 'number', 'attributes' => ['step' => '0.01']],
            ['name' => 'grand_total', 'label' => 'Total', 'type' => 'number', 'attributes' => ['step' => '0.01']],
            ['name' => 'invoice_type', 'label' => 'Tipo comprobante', 'type' => 'text'],
            ['name' => 'invoice_number', 'label' => 'N° comprobante', 'type' => 'text'],
            ['name' => 'electronic_authorization', 'label' => 'CAE / autorización', 'type' => 'text'],
            ['name' => 'invoiced_at', 'label' => 'Fecha facturación', 'type' => 'datetime'],
            ['name' => 'completed_at', 'label' => 'Fecha completada', 'type' => 'datetime'],
            ['name' => 'notes', 'label' => 'Notas', 'type' => 'textarea'],
        ]);
    }
}
