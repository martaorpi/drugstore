<?php

namespace App\Http\Controllers\Admin;

use App\Models\Branch;
use App\Models\PriceList;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;

class PriceListCrudController extends CrudController
{
    use CreateOperation;
    use DeleteOperation;
    use ListOperation;
    use ShowOperation;
    use UpdateOperation;

    public function setup(): void
    {
        $this->crud->setModel(PriceList::class);
        $this->crud->setRoute(config('backpack.base.route_prefix').'/price-lists');
        $this->crud->setEntityNameStrings('lista de precios', 'listas de precios');
    }

    protected function setupListOperation(): void
    {
        $this->crud->setTitle('Listas de precios');
        $this->crud->addColumns([
            [
                'name' => 'branch', 'label' => 'Sucursal', 'type' => 'select',
                'entity' => 'branch', 'attribute' => 'name', 'model' => Branch::class,
            ],
            ['name' => 'code', 'label' => 'Código', 'type' => 'text'],
            ['name' => 'name', 'label' => 'Nombre', 'type' => 'text'],
            ['name' => 'is_default', 'label' => 'Por defecto', 'type' => 'boolean'],
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
                'name' => 'branch_id', 'label' => 'Sucursal (vacío = todas)', 'type' => 'select',
                'entity' => 'branch', 'model' => Branch::class, 'attribute' => 'name',
                'allows_null' => true,
            ],
            ['name' => 'code', 'label' => 'Código', 'type' => 'text'],
            ['name' => 'name', 'label' => 'Nombre', 'type' => 'text'],
            ['name' => 'is_default', 'label' => 'Lista por defecto', 'type' => 'boolean'],
            ['name' => 'is_active', 'label' => 'Activa', 'type' => 'boolean', 'default' => true],
        ]);
    }
}
