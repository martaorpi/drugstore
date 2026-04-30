<?php

namespace App\Http\Controllers\Admin;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;

class ProductCrudController extends CrudController
{
    use CreateOperation;
    use DeleteOperation;
    use ListOperation;
    use ShowOperation;
    use UpdateOperation;

    public function setup(): void
    {
        $this->crud->setModel(Product::class);
        $this->crud->setRoute(config('backpack.base.route_prefix').'/products');
        $this->crud->setEntityNameStrings('producto', 'productos');
    }

    protected function setupListOperation(): void
    {
        $this->crud->setTitle('Productos');
        $this->crud->addColumns([
            ['name' => 'internal_code', 'label' => 'Código', 'type' => 'text'],
            ['name' => 'barcode', 'label' => 'EAN', 'type' => 'text'],
            ['name' => 'name', 'label' => 'Nombre', 'type' => 'text'],
            [
                'name' => 'category', 'label' => 'Categoría', 'type' => 'select',
                'entity' => 'category', 'attribute' => 'name', 'model' => Category::class,
            ],
            ['name' => 'sale_unit', 'label' => 'Unidad', 'type' => 'text'],
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
            'internal_code' => 'required|string|max:64|unique:products,internal_code',
            'name' => 'required|string|max:255',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
        ]);
        $this->addProductFields();
    }

    protected function setupUpdateOperation(): void
    {
        $id = $this->crud->getCurrentEntryId() ?? 0;
        $this->crud->setValidation([
            'internal_code' => 'required|string|max:64|unique:products,internal_code,'.$id,
            'name' => 'required|string|max:255',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
        ]);
        $this->addProductFields();
    }

    private function addProductFields(): void
    {
        $this->crud->addFields([
            ['name' => 'internal_code', 'label' => 'Código interno', 'type' => 'text'],
            ['name' => 'barcode', 'label' => 'Código de barras (EAN)', 'type' => 'text'],
            ['name' => 'name', 'label' => 'Nombre', 'type' => 'text'],
            ['name' => 'short_name', 'label' => 'Nombre corto', 'type' => 'text'],
            ['name' => 'description', 'label' => 'Descripción', 'type' => 'textarea'],
            [
                'name' => 'category_id', 'label' => 'Categoría', 'type' => 'select',
                'entity' => 'category', 'model' => Category::class, 'attribute' => 'name',
                'allows_null' => true,
            ],
            [
                'name' => 'brand_id', 'label' => 'Marca', 'type' => 'select',
                'entity' => 'brand', 'model' => Brand::class, 'attribute' => 'name',
                'allows_null' => true,
            ],
            [
                'name' => 'preferred_supplier_id', 'label' => 'Proveedor habitual', 'type' => 'select',
                'entity' => 'preferredSupplier', 'model' => Supplier::class, 'attribute' => 'business_name',
                'allows_null' => true,
            ],
            ['name' => 'sale_unit', 'label' => 'Unidad de venta', 'type' => 'text', 'default' => 'unidad', 'hint' => 'Ej.: unidad, pack, kg, litro.'],
            ['name' => 'units_per_pack', 'label' => 'Unidades por pack', 'type' => 'number', 'attributes' => ['step' => '0.0001']],
            ['name' => 'cost_average', 'label' => 'Costo promedio', 'type' => 'number', 'attributes' => ['step' => '0.0001']],
            ['name' => 'last_purchase_cost', 'label' => 'Último costo compra', 'type' => 'number', 'attributes' => ['step' => '0.0001']],
            ['name' => 'tax_rate', 'label' => 'IVA %', 'type' => 'number', 'attributes' => ['step' => '0.01'], 'default' => 21],
            ['name' => 'min_stock', 'label' => 'Stock mínimo alerta', 'type' => 'number', 'attributes' => ['step' => '0.0001']],
            [
                'name' => 'track_batches', 'label' => 'Controlar lotes y vencimiento', 'type' => 'boolean', 'default' => true,
                'hint' => 'Recomendado para productos con fecha de vencimiento (lácteos, fiambres, góndola refrigerada, etc.).',
            ],
            ['name' => 'is_active', 'label' => 'Activo', 'type' => 'boolean', 'default' => true],
        ]);
    }
}
