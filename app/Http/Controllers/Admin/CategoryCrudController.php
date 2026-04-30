<?php

namespace App\Http\Controllers\Admin;

use App\Models\Category;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;

class CategoryCrudController extends CrudController
{
    use CreateOperation;
    use DeleteOperation;
    use ListOperation;
    use ShowOperation;
    use UpdateOperation;

    public function setup(): void
    {
        $this->crud->setModel(Category::class);
        $this->crud->setRoute(config('backpack.base.route_prefix').'/categories');
        $this->crud->setEntityNameStrings('categoría', 'categorías');
    }

    protected function setupListOperation(): void
    {
        $this->crud->setTitle('Categorías');
        $this->crud->addColumns([
            ['name' => 'code', 'label' => 'Código', 'type' => 'text'],
            ['name' => 'name', 'label' => 'Nombre', 'type' => 'text'],
            [
                'name' => 'parent', 'label' => 'Padre', 'type' => 'select',
                'entity' => 'parent', 'attribute' => 'name', 'model' => Category::class,
            ],
            ['name' => 'sort_order', 'label' => 'Orden', 'type' => 'number'],
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
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:64|unique:categories,code',
        ]);
        $this->addFields();
    }

    protected function setupUpdateOperation(): void
    {
        $id = $this->crud->getCurrentEntryId() ?? 0;
        $this->crud->setValidation([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:64|unique:categories,code,'.$id,
        ]);
        $this->addFields();
    }

    private function addFields(): void
    {
        $this->crud->addFields([
            [
                'name' => 'parent_id', 'label' => 'Categoría padre', 'type' => 'select',
                'entity' => 'parent', 'model' => Category::class, 'attribute' => 'name',
                'allows_null' => true,
            ],
            ['name' => 'code', 'label' => 'Código', 'type' => 'text'],
            ['name' => 'name', 'label' => 'Nombre', 'type' => 'text'],
            ['name' => 'sort_order', 'label' => 'Orden', 'type' => 'number', 'default' => 0],
            ['name' => 'is_active', 'label' => 'Activa', 'type' => 'boolean', 'default' => true],
        ]);
    }
}
