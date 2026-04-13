<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Medicine;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderWorkflowTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test if a customer can create an order and stock is decremented correctly.
     */
    public function test_can_create_order_with_stock_impact(): void
    {
        $customer = Customer::factory()->create(['total_spent' => 0]);
        $medicine = Medicine::factory()->create(['stock_quantity' => 10, 'sell_price' => 500]);

        $response = $this->actingAs($customer, 'sanctum')->postJson('/api/v1/orders', [
            'items' => [
                ['medicine_id' => $medicine->id, 'quantity' => 2]
            ],
            'shipping_method' => 'express',
            'payment_screenshot' => 'testing_mode', // Handled by controller env check
            'address' => [
                'region' => 'Yangon',
                'township' => 'Tarmwe',
                'street' => '123 Test St',
                'house_number' => '45A'
            ]
        ]);

        $response->assertStatus(201);
        $response->assertJsonFragment(['grand_total' => 1000]);

        // Verify Database Integrity
        $this->assertEquals(8, $medicine->fresh()->stock_quantity);
        $this->assertEquals(1000, $customer->fresh()->total_spent);
    }

    /**
     * Test order failure due to insufficient stock.
     */
    public function test_order_fails_on_insufficient_stock(): void
    {
        $customer = Customer::factory()->create();
        $medicine = Medicine::factory()->create(['stock_quantity' => 1, 'sell_price' => 500]);

        $response = $this->actingAs($customer, 'sanctum')->postJson('/api/v1/orders', [
            'items' => [
                ['medicine_id' => $medicine->id, 'quantity' => 5]
            ],
            'shipping_method' => 'express',
            'address' => ['region' => 'Yangon', 'township' => 'Tarmwe']
        ]);

        $response->assertStatus(400);
        $response->assertJsonFragment(['message' => 'Order failed: Insufficient stock for ' . $medicine->name]);
    }
}
