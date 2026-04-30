<?php

namespace App\Http\Controllers\Admin;

use App\Models\Branch;
use App\Models\CashRegister;
use App\Models\Warehouse;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;

class CashRegisterCrudController extends CrudController
{
    use CreateOperation;
    use DeleteOperation;
    use ListOperation;
    use ShowOperation;
    use UpdateOperation;

    public function setup(): void
    {
        $this->crud->setModel(CashRegister::class);
        $this->crud->setRoute(config('backpack.base.route_prefix').'/cash-registers');
        $this->crud->setEntityNameStrings('caja', 'cajas');
    }

    protected function setupListOperation(): void
    {
        $this->crud->setTitle('Cajas registradoras');
        $this->crud->addColumns([
            [
                'name' => 'branch', 'label' => 'Sucursal', 'type' => 'select',
                'entity' => 'branch', 'attribute' => 'name', 'model' => Branch::class,
            ],
            ['name' => 'code', 'label' => 'Código', 'type' => 'text'],
            ['name' => 'name', 'label' => 'Nombre', 'type' => 'text'],
            ['name' => 'is_active', 'label' => 'Activa', 'type' => 'boolean'],
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
            'code' => 'required|string|max:32',
            'name' => 'required|string|max:255',
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
                'name' => 'branch_id', 'label' => 'Sucursal', 'type' => 'select',
                'entity' => 'branch', 'model' => Branch::class, 'attribute' => 'name',
            ],
            ['name' => 'code', 'label' => 'Código', 'type' => 'text'],
            ['name' => 'name', 'label' => 'Nombre', 'type' => 'text'],
            [
                'name' => 'default_warehouse_id', 'label' => 'Depósito por defecto', 'type' => 'select',
                'entity' => 'defaultWarehouse', 'model' => Warehouse::class, 'attribute' => 'name',
                'allows_null' => true,
            ],
            ['name' => 'is_active', 'label' => 'Activa', 'type' => 'boolean', 'default' => true],
        ]);
    }
}
