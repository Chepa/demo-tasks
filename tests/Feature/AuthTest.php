<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register(): void
    {
        $payload = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $response = $this->postJson('/api/register', $payload);

        $response
            ->assertCreated()
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonStructure([
                'success',
                'data' => [
                    'user' => [
                        'id',
                        'name',
                        'email',
                        'created_at',
                        'updated_at',
                    ],
                    'token',
                ],
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
        ]);
    }

    public function test_user_cannot_register_with_invalid_data(): void
    {
        $payload = [
            'name' => '',
            'email' => 'not-an-email',
            'password' => 'short',
            'password_confirmation' => 'mismatch',
        ];

        $response = $this->postJson('/api/register', $payload);

        $response->assertStatus(422);
    }

    public function test_user_can_login_with_correct_credentials(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
        ]);

        $payload = [
            'email' => 'test@example.com',
            'password' => 'password123',
        ];

        $response = $this->postJson('/api/login', $payload);

        $response
            ->assertOk()
            ->assertJson([
                'success' => true,
                'data' => [
                    'user' => [
                        'email' => 'test@example.com',
                    ],
                ],
            ])
            ->assertJsonStructure([
                'success',
                'data' => [
                    'user',
                    'token',
                ],
            ]);
    }

    public function test_user_cannot_login_with_wrong_credentials(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
        ]);

        $payload = [
            'email' => 'test@example.com',
            'password' => 'wrong-password',
        ];

        $response = $this->postJson('/api/login', $payload);

        $response
            ->assertStatus(401)
            ->assertJson([
                'success' => false,
            ]);
    }

    public function test_authenticated_user_can_logout(): void
    {
        $user = User::factory()->create();

        $token = $user->createToken('auth-token')->plainTextToken;

        $response = $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/logout');

        $response
            ->assertOk()
            ->assertJson([
                'success' => true,
            ]);
    }

    public function test_authenticated_user_can_get_own_profile(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
        ]);

        $token = $user->createToken('auth-token')->plainTextToken;

        $response = $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/user');

        $response
            ->assertOk()
            ->assertJson([
                'success' => true,
                'data' => [
                    'email' => 'test@example.com',
                ],
            ]);
    }
}


