<?php

namespace App\Http\Controllers\Admin;

use App\Models\Brand;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;

class BrandCrudController extends CrudController
{
    use CreateOperation;
    use DeleteOperation;
    use ListOperation;
    use ShowOperation;
    use UpdateOperation;

    public function setup(): void
    {
        $this->crud->setModel(Brand::class);
        $this->crud->setRoute(config('backpack.base.route_prefix').'/brands');
        $this->crud->setEntityNameStrings('marca', 'marcas');
    }

    protected function setupListOperation(): void
    {
        $this->crud->setTitle('Marcas');
        $this->crud->addColumns([
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
            'name' => 'required|string|max:255|unique:brands,name',
        ]);
        $this->crud->addFields([
            ['name' => 'name', 'label' => 'Nombre', 'type' => 'text'],
            ['name' => 'is_active', 'label' => 'Activa', 'type' => 'boolean', 'default' => true],
        ]);
    }

    protected function setupUpdateOperation(): void
    {
        $id = $this->crud->getCurrentEntryId() ?? 0;
        $this->crud->setValidation([
            'name' => 'required|string|max:255|unique:brands,name,'.$id,
        ]);
        $this->crud->addFields([
            ['name' => 'name', 'label' => 'Nombre', 'type' => 'text'],
            ['name' => 'is_active', 'label' => 'Activa', 'type' => 'boolean'],
        ]);
    }
}
