<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test Staff user cannot access global admin resources.
     */
    public function test_staff_cannot_access_admin_customer_management()
    {
        // Assuming your setup uses Spatie or custom role logic
        $staff = User::factory()->create(['role' => 'staff']);

        $response = $this->actingAs($staff, 'sanctum')->getJson('/api/v1/admin/customers');

        $response->assertStatus(403);
    }

    /**
     * Test Admin user can access statistical dashboard.
     */
    public function test_admin_can_access_dashboard_statistics()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin, 'sanctum')->getJson('/api/v1/admin/stats');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'status',
            'data' => [
                'total_customers',
                'total_medicines',
                'total_orders'
            ]
        ]);
    }

    public function test_admin_can_update_customer_status_with_supported_values()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $customer = Customer::factory()->create(['status' => 'pending']);

        $response = $this
            ->actingAs($admin, 'sanctum')
            ->putJson("/api/v1/admin/customers/{$customer->id}/status", [
                'status' => 'approved',
            ]);

        $response
            ->assertStatus(200)
            ->assertJsonPath('data.status', 'approved');
    }
}
