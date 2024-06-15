<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_valid_user_can_login()
    {
        $credentials = User::factory()->create()->only(['email']);

        $credentials['password'] = 'password';

        $response = $this->post(route('login'), (array) $credentials);

        $response->assertOk()
            ->assertJsonStructure([
                'access_token',
                'token_type',
            ]);
    }

    public function test_unauthorized_user_cannot_login()
    {
        $credentials = User::factory()->make()->only(['email', 'password']);

        $response = $this->post(route('login'), (array) $credentials);

        $response->assertStatus(401);

        $response->assertJson([
            'message' => 'Unauthorized',
        ]);
    }
}
