<?php

namespace App\Http\Controllers\Admin;

use App\Models\Branch;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;

class BranchCrudController extends CrudController
{
    use CreateOperation;
    use DeleteOperation;
    use ListOperation;
    use ShowOperation;
    use UpdateOperation;

    public function setup(): void
    {
        $this->crud->setModel(Branch::class);
        $this->crud->setRoute(config('backpack.base.route_prefix').'/branches');
        $this->crud->setEntityNameStrings('sucursal', 'sucursales');
    }

    protected function setupListOperation(): void
    {
        $this->crud->setTitle('Sucursales');
        $this->crud->addColumns([
            ['name' => 'code', 'label' => 'Código', 'type' => 'text'],
            ['name' => 'name', 'label' => 'Nombre', 'type' => 'text'],
            ['name' => 'city', 'label' => 'Ciudad', 'type' => 'text'],
            ['name' => 'phone', 'label' => 'Teléfono', 'type' => 'text'],
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
            'code' => 'required|string|max:32|unique:branches,code',
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
        ]);
        $this->addBranchFields();
    }

    protected function setupUpdateOperation(): void
    {
        $id = $this->crud->getCurrentEntryId() ?? 0;
        $this->crud->setValidation([
            'code' => 'required|string|max:32|unique:branches,code,'.$id,
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
        ]);
        $this->addBranchFields();
    }

    private function addBranchFields(): void
    {
        $this->crud->addFields([
            ['name' => 'code', 'label' => 'Código', 'type' => 'text', 'attributes' => ['required' => true]],
            ['name' => 'name', 'label' => 'Nombre', 'type' => 'text', 'attributes' => ['required' => true]],
            ['name' => 'address', 'label' => 'Dirección', 'type' => 'textarea'],
            ['name' => 'city', 'label' => 'Ciudad', 'type' => 'text'],
            ['name' => 'province', 'label' => 'Provincia', 'type' => 'text'],
            ['name' => 'postal_code', 'label' => 'CP', 'type' => 'text'],
            ['name' => 'phone', 'label' => 'Teléfono', 'type' => 'text'],
            ['name' => 'email', 'label' => 'Email', 'type' => 'email'],
            ['name' => 'tax_id', 'label' => 'CUIT / ID fiscal', 'type' => 'text'],
            ['name' => 'is_active', 'label' => 'Activa', 'type' => 'boolean', 'default' => true],
        ]);
    }
}
