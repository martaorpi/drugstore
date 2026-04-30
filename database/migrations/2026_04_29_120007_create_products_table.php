<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('internal_code', 64)->unique();
            $table->string('barcode', 64)->nullable()->index();
            $table->string('name');
            $table->string('short_name', 191)->nullable();
            $table->text('description')->nullable();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('brand_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('preferred_supplier_id')->nullable()->constrained('suppliers')->nullOnDelete();
            $table->string('sale_unit', 32)->default('unidad')->comment('unidad, pack, etc.');
            $table->decimal('units_per_pack', 12, 4)->nullable()->comment('Unidades contenidas si se vende por pack');
            $table->decimal('cost_average', 15, 4)->default(0);
            $table->decimal('last_purchase_cost', 15, 4)->nullable();
            $table->decimal('tax_rate', 5, 2)->default(21)->comment('IVA % por defecto en línea de venta');
            $table->decimal('min_stock', 12, 4)->default(0);
            $table->boolean('track_batches')->default(true)->comment('Lotes y vencimientos (útil en lácteos, fiambres, etc.)');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
