<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('products')) {
            return;
        }

        Schema::table('products', function (Blueprint $table) {
            $drops = [];
            foreach (['requires_prescription', 'controlled_schedule', 'anmat_registry'] as $col) {
                if (Schema::hasColumn('products', $col)) {
                    $drops[] = $col;
                }
            }
            if ($drops !== []) {
                $table->dropColumn($drops);
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('products')) {
            return;
        }

        Schema::table('products', function (Blueprint $table) {
            if (! Schema::hasColumn('products', 'requires_prescription')) {
                $table->boolean('requires_prescription')->default(false);
            }
            if (! Schema::hasColumn('products', 'controlled_schedule')) {
                $table->string('controlled_schedule', 8)->nullable();
            }
            if (! Schema::hasColumn('products', 'anmat_registry')) {
                $table->string('anmat_registry', 64)->nullable();
            }
        });
    }
};
