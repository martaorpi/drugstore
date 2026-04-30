<?php

namespace App\Http\Controllers\Admin;

use App\Models\Branch;
use App\Models\User;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;

class UserCrudController extends CrudController
{
    use CreateOperation;
    use DeleteOperation;
    use ListOperation;
    use ShowOperation;
    use UpdateOperation;

    public function setup(): void
    {
        $this->crud->setModel(User::class);
        $this->crud->setRoute(config('backpack.base.route_prefix').'/users');
        $this->crud->setEntityNameStrings('usuario', 'usuarios');
    }

    protected function setupListOperation(): void
    {
        $this->crud->setTitle('Usuarios');
        $this->crud->addColumns([
            ['name' => 'name', 'label' => 'Nombre', 'type' => 'text'],
            ['name' => 'email', 'label' => 'Email', 'type' => 'text'],
            ['name' => 'role', 'label' => 'Rol', 'type' => 'text'],
            [
                'name' => 'defaultBranch', 'label' => 'Sucursal', 'type' => 'select',
                'entity' => 'defaultBranch', 'attribute' => 'name', 'model' => Branch::class,
            ],
        ]);
    }

    protected function setupShowOperation(): void
    {
        $this->crud->set('show.setFromDb', true);
    }

    protected function setupCreateOperation(): void
    {
        $this->crud->setValidation([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8',
            'role' => 'required|string|max:32',
        ]);
        $this->crud->addFields($this->userFields(includePassword: true));
    }

    protected function setupUpdateOperation(): void
    {
        $id = $this->crud->getCurrentEntryId() ?? 0;
        $this->crud->setValidation([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,'.$id,
            'role' => 'required|string|max:32',
        ]);
        $this->crud->addFields($this->userFields(includePassword: false));
    }

    private function userFields(bool $includePassword): array
    {
        $fields = [
            ['name' => 'name', 'label' => 'Nombre', 'type' => 'text'],
            ['name' => 'email', 'label' => 'Email', 'type' => 'email'],
        ];
        if ($includePassword) {
            $fields[] = ['name' => 'password', 'label' => 'Contraseña', 'type' => 'password'];
        }
        $fields[] = [
            'name' => 'default_branch_id', 'label' => 'Sucursal por defecto', 'type' => 'select',
            'entity' => 'defaultBranch', 'model' => Branch::class, 'attribute' => 'name',
            'allows_null' => true,
        ];
        $fields[] = [
            'name' => 'role', 'label' => 'Rol', 'type' => 'select_from_array',
            'options' => [
                'admin' => 'Administrador',
                'manager' => 'Gerente',
                'cashier' => 'Cajero',
            ],
            'default' => 'cashier',
        ];

        return $fields;
    }
}
