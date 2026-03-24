<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Models\Medicine;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create Admin user
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('password123'),
            'role' => 'admin',
        ]);


        // Create Staff user
        User::create([
            'name' => 'Staff User',
            'email' => 'staff@gmail.com',
            'password' => bcrypt('password123'),
            'role' => 'staff',
        ]);

        // Create Categories
        $tablet = Category::create(['name' => 'Tablet', 'slug' => 'tablet', 'is_active' => true]);
        $syrup = Category::create(['name' => 'Syrup', 'slug' => 'syrup', 'is_active' => true]);

        // Create Sample Medicines
        Medicine::create([
            'category_id' => $tablet->id,
            'name' => 'Paracetamol 500mg',
            'sku_code' => 'TAB-001',
            'buy_price' => 500,
            'sell_price' => 800,
            'stock_quantity' => 100,
            'expiry_date' => '2027-12-31',
            'is_active' => true,
        ]);

        Medicine::create([
            'category_id' => $syrup->id,
            'name' => 'Biogesic Syrup',
            'sku_code' => 'SYR-001',
            'buy_price' => 1500,
            'sell_price' => 2000,
            'stock_quantity' => 50,
            'expiry_date' => '2026-06-15',
            'is_active' => true,
        ]);
    }
}
