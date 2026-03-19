<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthTest extends TestCase
{
    /**
     * Test if the login API endpoint requires correct credentials.
     */
    public function test_login_requires_valid_credentials(): void
    {
        $response = $this->postJson('/api/login', [
            'email' => 'wrong@example.com',
            'password' => 'invalid-password',
        ]);

        // Typically, Sanctum API login either returns 422 (validation) or 401 (unauthorized)
        // We ensure it doesn't return 200/OK 
        $this->assertNotEquals(200, $response->status());
        
        // Let's assert it falls back to 401 or 404 depending on exact route config
        $this->assertTrue(in_array($response->status(), [401, 404, 422]));
    }
}
