<?php

namespace Tests\Feature;

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
        $staff = \App\Models\User::factory()->create(['role' => 'staff']);

        $response = $this->actingAs($staff, 'sanctum')->getJson('/api/v1/admin/customers');
        
        $response->assertStatus(403);
    }

    /**
     * Test Admin user can access statistical dashboard.
     */
    public function test_admin_can_access_dashboard_statistics()
    {
        $admin = \App\Models\User::factory()->create(['role' => 'admin']);

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
}
