<?php

namespace App\Http\Controllers\Admin;

use App\Models\Customer;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;

class CustomerCrudController extends CrudController
{
    use CreateOperation;
    use DeleteOperation;
    use ListOperation;
    use ShowOperation;
    use UpdateOperation;

    public function setup(): void
    {
        $this->crud->setModel(Customer::class);
        $this->crud->setRoute(config('backpack.base.route_prefix').'/customers');
        $this->crud->setEntityNameStrings('cliente', 'clientes');
    }

    protected function setupListOperation(): void
    {
        $this->crud->setTitle('Clientes');
        $this->crud->addColumns([
            ['name' => 'code', 'label' => 'Código', 'type' => 'text'],
            ['name' => 'display_name', 'label' => 'Nombre', 'type' => 'text'],
            ['name' => 'tax_id', 'label' => 'DNI/CUIT', 'type' => 'text'],
            ['name' => 'phone', 'label' => 'Teléfono', 'type' => 'text'],
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
            'display_name' => 'required|string|max:255',
            'code' => 'nullable|string|max:64|unique:customers,code',
            'email' => 'nullable|email',
        ]);
        $this->addFields();
    }

    protected function setupUpdateOperation(): void
    {
        $id = $this->crud->getCurrentEntryId() ?? 0;
        $this->crud->setValidation([
            'display_name' => 'required|string|max:255',
            'code' => 'nullable|string|max:64|unique:customers,code,'.$id,
            'email' => 'nullable|email',
        ]);
        $this->addFields();
    }

    private function addFields(): void
    {
        $this->crud->addFields([
            ['name' => 'code', 'label' => 'Código interno', 'type' => 'text'],
            ['name' => 'display_name', 'label' => 'Nombre / razón social', 'type' => 'text'],
            ['name' => 'tax_id', 'label' => 'DNI / CUIT', 'type' => 'text'],
            ['name' => 'tax_condition', 'label' => 'Condición fiscal', 'type' => 'text'],
            ['name' => 'email', 'label' => 'Email', 'type' => 'email'],
            ['name' => 'phone', 'label' => 'Teléfono', 'type' => 'text'],
            ['name' => 'address', 'label' => 'Dirección', 'type' => 'textarea'],
            ['name' => 'city', 'label' => 'Ciudad', 'type' => 'text'],
            ['name' => 'credit_limit', 'label' => 'Límite de crédito', 'type' => 'number', 'attributes' => ['step' => '0.01']],
            ['name' => 'is_active', 'label' => 'Activo', 'type' => 'boolean', 'default' => true],
        ]);
    }
}
