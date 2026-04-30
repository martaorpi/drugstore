<?php

namespace Database\Seeders;

use App\Models\PaymentMethod;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        foreach (
            [
                ['code' => 'cash', 'name' => 'Efectivo', 'sort_order' => 10],
                ['code' => 'debit', 'name' => 'Débito', 'sort_order' => 20],
                ['code' => 'credit', 'name' => 'Crédito', 'sort_order' => 30],
                ['code' => 'mp', 'name' => 'Mercado Pago', 'requires_reference' => true, 'sort_order' => 40],
            ] as $pm
        ) {
            PaymentMethod::updateOrCreate(
                ['code' => $pm['code']],
                array_merge(['is_active' => true, 'requires_reference' => false], $pm)
            );
        }

        User::updateOrCreate(
            ['email' => 'admin@admin'],
            [
                'name' => 'Administrador',
                'password' => 'password',
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        $this->call(DrugstoreDemoSeeder::class);
    }
}
