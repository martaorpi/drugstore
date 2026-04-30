<?php

namespace App\Http\Controllers\Admin;

use App\Models\PaymentMethod;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;

class PaymentMethodCrudController extends CrudController
{
    use CreateOperation;
    use DeleteOperation;
    use ListOperation;
    use ShowOperation;
    use UpdateOperation;

    public function setup(): void
    {
        $this->crud->setModel(PaymentMethod::class);
        $this->crud->setRoute(config('backpack.base.route_prefix').'/payment-methods');
        $this->crud->setEntityNameStrings('medio de pago', 'medios de pago');
    }

    protected function setupListOperation(): void
    {
        $this->crud->setTitle('Medios de pago');
        $this->crud->addColumns([
            ['name' => 'code', 'label' => 'Código', 'type' => 'text'],
            ['name' => 'name', 'label' => 'Nombre', 'type' => 'text'],
            ['name' => 'requires_reference', 'label' => 'Requiere ref.', 'type' => 'boolean'],
            ['name' => 'sort_order', 'label' => 'Orden', 'type' => 'number'],
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
            'code' => 'required|string|max:32|unique:payment_methods,code',
            'name' => 'required|string|max:255',
        ]);
        $this->addFields();
    }

    protected function setupUpdateOperation(): void
    {
        $id = $this->crud->getCurrentEntryId() ?? 0;
        $this->crud->setValidation([
            'code' => 'required|string|max:32|unique:payment_methods,code,'.$id,
            'name' => 'required|string|max:255',
        ]);
        $this->addFields();
    }

    private function addFields(): void
    {
        $this->crud->addFields([
            ['name' => 'code', 'label' => 'Código (ej. cash, mp)', 'type' => 'text'],
            ['name' => 'name', 'label' => 'Nombre', 'type' => 'text'],
            ['name' => 'requires_reference', 'label' => 'Requiere referencia', 'type' => 'boolean'],
            ['name' => 'sort_order', 'label' => 'Orden', 'type' => 'number', 'default' => 0],
            ['name' => 'is_active', 'label' => 'Activo', 'type' => 'boolean', 'default' => true],
        ]);
    }
}
