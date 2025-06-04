<?php

namespace Tests\Feature\Chat;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SendMessageTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_send_message_in_private_chat()
    {
        $user = User::factory()->create();
        $recipient = User::factory()->create();
        $token = $this->authenticate($user);

        $chat = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson("/api/chats/private/{$recipient->id}", [
            'body' => 'Initial message',
        ])->json();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson("/api/chats/{$chat['id']}/messages", [
            'body' => 'Hello world',
        ]);

        $response->assertOk()
            ->assertJsonStructure(['id', 'body', 'sender_id', 'chat_id']);
    }
}
