<?php

namespace Tests\Feature\Chat;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CreatePrivateChatTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_private_chat()
    {
        $sender = User::factory()->create();
        $recipient = User::factory()->create();

        $token = $this->authenticate($sender);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson("/api/chats/private/{$recipient->id}", [
            'body' => 'Hello there!',
        ]);

        $response->assertOk()
            ->assertJsonStructure(['id', 'type', 'created_at']);
    }
}
