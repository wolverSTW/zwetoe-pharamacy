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
        User::firstOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Admin User',
                'password' => 'admin123',
                'role' => 'admin',
                'is_approve' => true,
            ]
        );


        // Create Staff user
        User::firstOrCreate(
            ['email' => 'staff@gmail.com'],
            [
                'name' => 'Staff User',
                'password' => 'staff123',
                'role' => 'staff',
                'is_approve' => true,
            ]
        );

        // Create Categories
        /** @var Category $tablet */
        $tablet = Category::firstOrCreate(['slug' => 'tablet'], ['name' => 'Tablet', 'is_active' => true]);
        /** @var Category $syrup */
        $syrup = Category::firstOrCreate(['slug' => 'syrup'], ['name' => 'Syrup', 'is_active' => true]);

        if (!$tablet || !$syrup) {
            throw new \Exception('Failed to seed categories');
        }

        // Create Sample Medicines
        /** @var Medicine $med1 */
        $med1 = Medicine::firstOrCreate(
            ['sku_code' => 'TAB-001'],
            [
                'category_id' => $tablet->id,
                'name' => 'Paracetamol 500mg',
                'buy_price' => 500,
                'sell_price' => 800,
                'stock_quantity' => 100,
                'expiry_date' => '2027-12-31',
                'is_active' => true,
            ]
        );

        /** @var Medicine $med2 */
        $med2 = Medicine::firstOrCreate(
            ['sku_code' => 'SYR-001'],
            [
                'category_id' => $syrup->id,
                'name' => 'Biogesic Syrup',
                'buy_price' => 1500,
                'sell_price' => 2000,
                'stock_quantity' => 50,
                'expiry_date' => '2026-06-15',
                'is_active' => true,
            ]
        );

        if (!$med1 || !$med2) {
            throw new \Exception('Failed to seed medicines');
        }
        // Create Sample Customer
        /** @var Customer $customer */
        $customer = Customer::firstOrCreate(
            ['email' => 'customer@gmail.com'],
            [
                'name' => 'John Doe',
                'phone' => '09123456789',
                'password' => 'customer123',
                'status' => 'approved',
            ]
        );

        if (!$customer) {
            throw new \Exception('Failed to seed customer');
        }

        // Create Sample Orders
        $order1 = Order::create([
            'customer_id' => $customer->id,
            'total_amount' => 1600,
            'status' => 'completed',
            'payment_status' => 'paid',
            'created_at' => now()->subDays(2),
        ]);

        if (!$order1) {
            throw new \Exception('Failed to seed order 1');
        }

        $order1->items()->create([
            'medicine_id' => $med1->id,
            'quantity' => 2,
            'unit_price' => 800,
            'subtotal' => 1600,
        ]);

        $order2 = Order::create([
            'customer_id' => $customer->id,
            'total_amount' => 2000,
            'status' => 'pending',
            'payment_status' => 'pending',
            'created_at' => now()->subDay(),
        ]);

        if (!$order2) {
            throw new \Exception('Failed to seed order 2');
        }

        $order2->items()->create([
            'medicine_id' => $med2->id,
            'quantity' => 1,
            'unit_price' => 2000,
            'subtotal' => 2000,
        ]);
    }
}
