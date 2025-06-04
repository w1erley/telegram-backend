<?php

namespace Tests\Feature\Chat;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DeleteMessageTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_delete_their_message()
    {
        $user = User::factory()->create();
        $token = $this->authenticate($user);

        $recipient = User::factory()->create();

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept'        => 'application/json',
        ]);

        $chat = $this->postJson("/api/chats/private/{$recipient->id}", [
            'body' => 'Initial message'
        ])->json();

        $message = $this->postJson("/api/chats/{$chat['id']}/messages", [
            'body' => 'Some message'
        ])->json();

        $response = $this->deleteJson("/api/chats/{$chat['id']}/messages/{$message['id']}");

        $response->assertOk();
        $this->assertDatabaseMissing('messages', ['id' => $message['id']]);
    }
}
