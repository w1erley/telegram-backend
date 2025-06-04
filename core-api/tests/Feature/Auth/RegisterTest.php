<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register_successfully()
    {
        $response = $this->postJson('/api/register', [
            'username' => 'testuser',
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'secret123',
        ]);

        $response->assertStatus(201);

        $response->assertJsonStructure([
            'user' => ['id', 'username', 'name', 'email']
        ]);

        $this->assertDatabaseHas('users', ['email' => 'test@example.com']);
    }

    public function test_register_validation_fails_without_required_fields()
    {
        $response = $this->postJson('/api/register', []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'username', 'name', 'email', 'password',
        ]);
    }

    public function test_register_fails_with_existing_email_or_username()
    {
        User::factory()->create([
            'email' => 'duplicate@example.com',
            'username' => 'duplicateuser',
        ]);

        $response = $this->postJson('/api/register', [
            'username' => 'duplicateuser',
            'name' => 'Another',
            'email' => 'duplicate@example.com',
            'password' => 'secret123',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['email', 'username']);
    }
}