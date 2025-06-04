<?php

namespace Tests\Feature\Chat;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class FetchMessagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_fetch_messages_from_private_chat()
    {
        $user = User::factory()->create();
        $recipient = User::factory()->create();
        $token = $this->authenticate($user);

        $chat = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson("/api/chats/private/{$recipient->id}", [
            'body' => 'Initial message',
        ])->json();

        $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->postJson("/api/chats/{$chat['id']}/messages", ['body' => 'Message 1']);

        $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->postJson("/api/chats/{$chat['id']}/messages", ['body' => 'Message 2']);

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->getJson("/api/chats/{$chat['id']}/messages");

        $this->assertCount(3, $response->json());
    }
}
