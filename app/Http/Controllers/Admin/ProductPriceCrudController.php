<?php

namespace App\Http\Controllers\Admin;

use App\Models\PriceList;
use App\Models\Product;
use App\Models\ProductPrice;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;

class ProductPriceCrudController extends CrudController
{
    use CreateOperation;
    use DeleteOperation;
    use ListOperation;
    use ShowOperation;
    use UpdateOperation;

    public function setup(): void
    {
        $this->crud->setModel(ProductPrice::class);
        $this->crud->setRoute(config('backpack.base.route_prefix').'/product-prices');
        $this->crud->setEntityNameStrings('precio', 'precios por lista');
    }

    protected function setupListOperation(): void
    {
        $this->crud->setTitle('Precios por lista');
        $this->crud->addColumns([
            [
                'name' => 'product', 'label' => 'Producto', 'type' => 'select',
                'entity' => 'product', 'attribute' => 'name', 'model' => Product::class,
            ],
            [
                'name' => 'priceList', 'label' => 'Lista', 'type' => 'select',
                'entity' => 'priceList', 'attribute' => 'name', 'model' => PriceList::class,
            ],
            ['name' => 'price', 'label' => 'Precio', 'type' => 'number', 'decimals' => 2],
        ]);
    }

    protected function setupShowOperation(): void
    {
        $this->crud->set('show.setFromDb', true);
    }

    protected function setupCreateOperation(): void
    {
        $this->crud->setValidation([
            'product_id' => 'required|exists:products,id',
            'price_list_id' => 'required|exists:price_lists,id',
            'price' => 'required|numeric|min:0',
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
                'name' => 'product_id', 'label' => 'Producto', 'type' => 'select',
                'entity' => 'product', 'model' => Product::class, 'attribute' => 'name',
            ],
            [
                'name' => 'price_list_id', 'label' => 'Lista de precios', 'type' => 'select',
                'entity' => 'priceList', 'model' => PriceList::class, 'attribute' => 'name',
            ],
            ['name' => 'price', 'label' => 'Precio', 'type' => 'number', 'attributes' => ['step' => '0.0001']],
            ['name' => 'valid_from', 'label' => 'Válido desde', 'type' => 'date'],
            ['name' => 'valid_until', 'label' => 'Válido hasta', 'type' => 'date'],
        ]);
    }
}
