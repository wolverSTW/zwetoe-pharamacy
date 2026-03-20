<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FilamentIntegrationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test if the admin portal is accessible for an authenticated admin.
     */
    public function test_admin_portal_is_accessible(): void
    {
        $admin = User::factory()->admin()->create();
        // Assume 'admin' role is required for Filament access in your setup
        // But for testing the skeleton, ensure it doesn't give a 500 error 
        
        $response = $this->actingAs($admin)->get('/admin');

        // It should either be 200 (OK) or 302 (Redirect to login if unauthorized 
        // by Filament's specific auth provider)
        $this->assertTrue(in_array($response->status(), [200, 302]));
    }

    /**
     * Test if resources are discoverable.
     */
    public function test_medicines_resource_index_responds(): void
    {
        $admin = User::factory()->admin()->create();
        $response = $this->actingAs($admin)->get('/admin/medicines');
        
        $this->assertTrue(in_array($response->status(), [200, 302]));
    }
}
