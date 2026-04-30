<?php

namespace App\Http\Controllers\Admin;

use App\Models\PaymentMethod;
use App\Models\Sale;
use App\Models\SalePayment;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;

class SalePaymentCrudController extends CrudController
{
    use CreateOperation;
    use DeleteOperation;
    use ListOperation;
    use ShowOperation;
    use UpdateOperation;

    public function setup(): void
    {
        $this->crud->setModel(SalePayment::class);
        $this->crud->setRoute(config('backpack.base.route_prefix').'/sale-payments');
        $this->crud->setEntityNameStrings('cobro', 'cobros');
    }

    protected function setupListOperation(): void
    {
        $this->crud->setTitle('Cobros de ventas');
        $this->crud->addColumns([
            [
                'name' => 'sale', 'label' => 'Venta', 'type' => 'select',
                'entity' => 'sale', 'attribute' => 'sale_number', 'model' => Sale::class,
            ],
            [
                'name' => 'paymentMethod', 'label' => 'Medio', 'type' => 'select',
                'entity' => 'paymentMethod', 'attribute' => 'name', 'model' => PaymentMethod::class,
            ],
            ['name' => 'amount', 'label' => 'Importe', 'type' => 'number', 'decimals' => 2],
            ['name' => 'reference', 'label' => 'Referencia', 'type' => 'text'],
        ]);
    }

    protected function setupShowOperation(): void
    {
        $this->crud->set('show.setFromDb', true);
    }

    protected function setupCreateOperation(): void
    {
        $this->crud->setValidation([
            'sale_id' => 'required|exists:sales,id',
            'payment_method_id' => 'required|exists:payment_methods,id',
            'amount' => 'required|numeric|min:0',
        ]);
        $this->crud->addFields([
            [
                'name' => 'sale_id', 'label' => 'Venta', 'type' => 'select',
                'entity' => 'sale', 'model' => Sale::class, 'attribute' => 'sale_number',
            ],
            [
                'name' => 'payment_method_id', 'label' => 'Medio de pago', 'type' => 'select',
                'entity' => 'paymentMethod', 'model' => PaymentMethod::class, 'attribute' => 'name',
            ],
            ['name' => 'amount', 'label' => 'Importe', 'type' => 'number', 'attributes' => ['step' => '0.01']],
            ['name' => 'reference', 'label' => 'Referencia', 'type' => 'text'],
        ]);
    }

    protected function setupUpdateOperation(): void
    {
        $this->setupCreateOperation();
    }
}
