<?php

namespace App\Http\Controllers\Admin;

use App\Models\CashRegister;
use App\Models\CashSession;
use App\Models\User;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;

class CashSessionCrudController extends CrudController
{
    use CreateOperation;
    use DeleteOperation;
    use ListOperation;
    use ShowOperation;
    use UpdateOperation;

    public function setup(): void
    {
        $this->crud->setModel(CashSession::class);
        $this->crud->setRoute(config('backpack.base.route_prefix').'/cash-sessions');
        $this->crud->setEntityNameStrings('sesión de caja', 'sesiones de caja');
    }

    protected function setupListOperation(): void
    {
        $this->crud->setTitle('Sesiones de caja');
        $this->crud->addColumns([
            [
                'name' => 'cashRegister', 'label' => 'Caja', 'type' => 'select',
                'entity' => 'cashRegister', 'attribute' => 'name', 'model' => CashRegister::class,
            ],
            [
                'name' => 'user', 'label' => 'Usuario', 'type' => 'select',
                'entity' => 'user', 'attribute' => 'name', 'model' => User::class,
            ],
            ['name' => 'opened_at', 'label' => 'Apertura', 'type' => 'datetime'],
            ['name' => 'closed_at', 'label' => 'Cierre', 'type' => 'datetime'],
            ['name' => 'status', 'label' => 'Estado', 'type' => 'text'],
        ]);
    }

    protected function setupShowOperation(): void
    {
        $this->crud->set('show.setFromDb', true);
    }

    protected function setupCreateOperation(): void
    {
        $this->crud->setValidation([
            'cash_register_id' => 'required|exists:cash_registers,id',
            'user_id' => 'required|exists:users,id',
            'opened_at' => 'required|date',
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
            [
                'name' => 'cash_register_id', 'label' => 'Caja', 'type' => 'select',
                'entity' => 'cashRegister', 'model' => CashRegister::class, 'attribute' => 'name',
            ],
            [
                'name' => 'user_id', 'label' => 'Usuario', 'type' => 'select',
                'entity' => 'user', 'model' => User::class, 'attribute' => 'name',
            ],
            ['name' => 'opened_at', 'label' => 'Apertura', 'type' => 'datetime'],
            ['name' => 'closed_at', 'label' => 'Cierre', 'type' => 'datetime'],
            ['name' => 'opening_float', 'label' => 'Fondo inicial', 'type' => 'number', 'attributes' => ['step' => '0.01']],
            ['name' => 'closing_float', 'label' => 'Arqueo cierre', 'type' => 'number', 'attributes' => ['step' => '0.01']],
            ['name' => 'expected_cash', 'label' => 'Efectivo esperado', 'type' => 'number', 'attributes' => ['step' => '0.01']],
            ['name' => 'counted_cash', 'label' => 'Efectivo contado', 'type' => 'number', 'attributes' => ['step' => '0.01']],
            [
                'name' => 'status', 'label' => 'Estado', 'type' => 'select_from_array',
                'options' => ['open' => 'Abierta', 'closed' => 'Cerrada'],
                'default' => 'open',
            ],
            ['name' => 'notes', 'label' => 'Notas', 'type' => 'textarea'],
        ]);
    }
}
