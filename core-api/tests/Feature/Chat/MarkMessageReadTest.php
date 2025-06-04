<?php

namespace Tests\Feature\Chat;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MarkMessageReadTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_mark_message_as_read()
    {
        $sender = User::factory()->create();
        $receiver = User::factory()->create();

        $senderToken = $this->authenticate($sender);
        $receiverToken = $this->authenticate($receiver);

        $chat = $this->withHeaders([
            'Authorization' => 'Bearer ' . $senderToken,
        ])->postJson("/api/chats/private/{$receiver->id}", [
            'body' => 'Initial message',
        ])->json();

        $message = $this->withHeaders([
            'Authorization' => 'Bearer ' . $senderToken,
        ])->postJson("/api/chats/{$chat['id']}/messages", ['body' => 'Unread message'])->json();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $receiverToken,
        ])->postJson("/api/chats/{$chat['id']}/messages/{$message['id']}/read");

        $response->assertOk();
        $this->assertDatabaseHas('message_stats', [
            'message_id' => $message['id'],
            'user_id'    => $receiver->id,
        ]);
    }
}
