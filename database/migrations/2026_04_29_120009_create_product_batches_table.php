<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('warehouse_id')->constrained()->cascadeOnDelete();
            $table->string('lot_number', 64);
            $table->date('expires_on')->nullable();
            $table->decimal('quantity_on_hand', 14, 4)->default(0);
            $table->decimal('unit_cost', 15, 4)->default(0);
            $table->foreignId('supplier_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();

            $table->index(['warehouse_id', 'product_id']);
            $table->index('expires_on');
            $table->unique(['warehouse_id', 'product_id', 'lot_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_batches');
    }
};
