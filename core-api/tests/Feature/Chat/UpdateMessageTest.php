<?php

namespace Tests\Feature\Chat;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UpdateMessageTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_update_their_message()
    {
        $user = User::factory()->create();
        $token = $this->authenticate($user);
        $recipient = User::factory()->create();

        $chat = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson("/api/chats/private/{$recipient->id}", [
            'body' => 'Initial message',
        ])->json();

        $message = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson("/api/chats/{$chat['id']}/messages", [
            'body' => 'Old message',
        ])->json();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->patchJson("/api/chats/{$chat['id']}/messages/{$message['id']}", [
            'body' => 'Updated message',
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('messages', ['id' => $message['id'], 'body' => 'Updated message']);
    }
}
