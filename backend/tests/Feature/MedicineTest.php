<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MedicineTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_fetch_medicines()
    {
        $response = $this->getJson('/api/v1/medicines');
        $response->assertStatus(200);
    }

    public function test_can_search_medicines()
    {
        $response = $this->getJson('/api/v1/search?query=paracetamol');
        $response->assertStatus(200);
    }
}
