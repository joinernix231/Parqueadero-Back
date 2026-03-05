<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_login_with_valid_credentials(): void
    {
        // Arrange
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
            'role' => 'operator',
        ]);

        // Act
        $response = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        // Assert
        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'token',
                    'user' => ['id', 'name', 'email', 'role'],
                ],
            ]);

        $this->assertNotNull($response->json('data.token'));
        $this->assertEquals('test@example.com', $response->json('data.user.email'));
    }

    public function test_user_can_logout(): void
    {
        // Arrange
        $user = User::factory()->create([
            'password' => bcrypt('password'),
        ]);
        $token = $user->createToken('test-token')->plainTextToken;

        // Act
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/logout');

        // Assert
        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Sesión cerrada exitosamente',
            ]);
    }

    public function test_user_cannot_login_with_invalid_credentials(): void
    {
        // Arrange
        User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        // Act
        $response = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'wrong-password',
        ]);

        // Assert
        $response->assertStatus(401);
    }
}

