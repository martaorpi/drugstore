<?php

namespace App\Http\Controllers\Admin;

use App\Models\Branch;
use App\Models\Warehouse;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;

class WarehouseCrudController extends CrudController
{
    use CreateOperation;
    use DeleteOperation;
    use ListOperation;
    use ShowOperation;
    use UpdateOperation;

    public function setup(): void
    {
        $this->crud->setModel(Warehouse::class);
        $this->crud->setRoute(config('backpack.base.route_prefix').'/warehouses');
        $this->crud->setEntityNameStrings('depósito', 'depósitos');
    }

    protected function setupListOperation(): void
    {
        $this->crud->setTitle('Depósitos');
        $this->crud->addColumns([
            [
                'name' => 'branch', 'label' => 'Sucursal', 'type' => 'select',
                'entity' => 'branch', 'attribute' => 'name', 'model' => Branch::class,
            ],
            ['name' => 'code', 'label' => 'Código', 'type' => 'text'],
            ['name' => 'name', 'label' => 'Nombre', 'type' => 'text'],
            ['name' => 'is_default', 'label' => 'Por defecto', 'type' => 'boolean'],
            ['name' => 'is_active', 'label' => 'Activo', 'type' => 'boolean'],
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
        $this->addWarehouseFields();
    }

    protected function setupUpdateOperation(): void
    {
        $this->setupCreateOperation();
    }

    private function addWarehouseFields(): void
    {
        $this->crud->addFields([
            [
                'name' => 'branch_id', 'label' => 'Sucursal', 'type' => 'select',
                'entity' => 'branch', 'model' => Branch::class, 'attribute' => 'name',
            ],
            ['name' => 'code', 'label' => 'Código', 'type' => 'text'],
            ['name' => 'name', 'label' => 'Nombre', 'type' => 'text'],
            ['name' => 'is_default', 'label' => 'Depósito por defecto', 'type' => 'boolean'],
            ['name' => 'is_active', 'label' => 'Activo', 'type' => 'boolean', 'default' => true],
        ]);
    }
}
