<?php

namespace App\Http\Controllers\Admin;

use App\Models\Supplier;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;

class SupplierCrudController extends CrudController
{
    use CreateOperation;
    use DeleteOperation;
    use ListOperation;
    use ShowOperation;
    use UpdateOperation;

    public function setup(): void
    {
        $this->crud->setModel(Supplier::class);
        $this->crud->setRoute(config('backpack.base.route_prefix').'/suppliers');
        $this->crud->setEntityNameStrings('proveedor', 'proveedores');
    }

    protected function setupListOperation(): void
    {
        $this->crud->setTitle('Proveedores');
        $this->crud->addColumns([
            ['name' => 'code', 'label' => 'Código', 'type' => 'text'],
            ['name' => 'business_name', 'label' => 'Razón social', 'type' => 'text'],
            ['name' => 'tax_id', 'label' => 'CUIT', 'type' => 'text'],
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
            'code' => 'required|string|max:64|unique:suppliers,code',
            'business_name' => 'required|string|max:255',
            'email' => 'nullable|email',
        ]);
        $this->addFields();
    }

    protected function setupUpdateOperation(): void
    {
        $id = $this->crud->getCurrentEntryId() ?? 0;
        $this->crud->setValidation([
            'code' => 'required|string|max:64|unique:suppliers,code,'.$id,
            'business_name' => 'required|string|max:255',
            'email' => 'nullable|email',
        ]);
        $this->addFields();
    }

    private function addFields(): void
    {
        $this->crud->addFields([
            ['name' => 'code', 'label' => 'Código', 'type' => 'text'],
            ['name' => 'business_name', 'label' => 'Razón social', 'type' => 'text'],
            ['name' => 'tax_id', 'label' => 'CUIT', 'type' => 'text'],
            ['name' => 'contact_name', 'label' => 'Contacto', 'type' => 'text'],
            ['name' => 'phone', 'label' => 'Teléfono', 'type' => 'text'],
            ['name' => 'email', 'label' => 'Email', 'type' => 'email'],
            ['name' => 'address', 'label' => 'Dirección', 'type' => 'textarea'],
            ['name' => 'city', 'label' => 'Ciudad', 'type' => 'text'],
            ['name' => 'notes', 'label' => 'Notas', 'type' => 'textarea'],
            ['name' => 'is_active', 'label' => 'Activo', 'type' => 'boolean', 'default' => true],
        ]);
    }
}
