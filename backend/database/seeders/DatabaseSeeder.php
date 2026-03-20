<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Models\Medicine;
use App\Models\Order;
use App\Models\Customer;
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
            'is_approve' => true,
        ]);


        // Create Staff user
        User::create([
            'name' => 'Staff User',
            'email' => 'staff@gmail.com',
            'password' => bcrypt('password123'),
            'role' => 'staff',
            'is_approve' => true,
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
        // Create Sample Customer
        $customer = Customer::create([
            'name' => 'John Doe',
            'email' => 'customer@gmail.com',
            'phone' => '09123456789',
            'password' => 'password123',
            'status' => 'approved',
        ]);

        // Create Sample Orders
        $order1 = Order::create([
            'customer_id' => $customer->id,
            'total_amount' => 1600,
            'status' => 'completed',
            'payment_status' => 'paid',
            'created_at' => now()->subDays(2),
        ]);

        $order1->items()->create([
            'medicine_id' => 1,
            'quantity' => 2,
            'unit_price' => 800,
            'subtotal' => 1600,
        ]);

        $order2 = Order::create([
            'customer_id' => 1,
            'total_amount' => 2000,
            'status' => 'pending',
            'payment_status' => 'pending',
            'created_at' => now()->subDay(),
        ]);

        $order2->items()->create([
            'medicine_id' => 2,
            'quantity' => 1,
            'unit_price' => 2000,
            'subtotal' => 2000,
        ]);
    }
}
