<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Medicine;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CoreServiceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test medicine listing and search.
     */
    public function test_can_list_medicines_with_pagination(): void
    {
        Medicine::factory()->count(5)->create();

        $response = $this->getJson('/api/v1/medicines');

        $response->assertStatus(200);
        $response->assertJsonCount(5, 'data');
    }

    /**
     * Test category filtering.
     */
    public function test_can_filter_medicines_by_category(): void
    {
        $categoryA = Category::factory()->create(['name' => 'Tablets']);
        $categoryB = Category::factory()->create(['name' => 'Syrups']);

        Medicine::factory()->create(['category_id' => $categoryA->id, 'name' => 'Aspirin']);
        Medicine::factory()->create(['category_id' => $categoryB->id, 'name' => 'Cough Syrup']);

        // Filter by category A
        $response = $this->getJson("/api/v1/medicines?category_id={$categoryA->id}");

        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');
        $response->assertJsonFragment(['name' => 'Aspirin']);
    }

    /**
     * Test admin statistics access.
     */
    public function test_non_admin_cannot_access_dashboard_stats(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->getJson('/api/v1/admin/stats');

        // If your app uses Spatie permissions, ensure role 'admin' is required
        $response->assertStatus(403);
    }
}
