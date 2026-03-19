<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_fetch_categories()
    {
        // Even if the DB is empty, the endpoint should return 200
        $response = $this->getJson('/api/v1/categories');

        $response->assertStatus(200);
        // We expect a JSON structure (e.g., array of items or dict with 'data' if paginated)
    }
}
