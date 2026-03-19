<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_create_order()
    {
        $response = $this->postJson('/api/v1/orders', [
            'items' => []
        ]);

        $response->assertStatus(401); // Sanctum returns 401 for unauthenticated
    }
}
