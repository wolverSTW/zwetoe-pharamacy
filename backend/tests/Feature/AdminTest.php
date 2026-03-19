<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_admin_stats()
    {
        $response = $this->getJson('/api/v1/admin/stats');
        
        // Ensure that unauthorized users hit a 401 (Unauthenticated) 
        // before they even hit a 403 (Unauthorized)
        $response->assertStatus(401);
    }
}
