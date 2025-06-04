<?php

namespace Tests\Feature\Chat;

use App\Models\Chat;
use App\Models\Message;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_search_returns_chats_users_and_messages()
    {
        $user = User::factory()->create(['username' => 'john_doe', 'name' => 'John']);
        $token = $this->authenticate($user);

        $recipient = User::factory()->create(['username' => 'jane_doe', 'name' => 'Jane']);
        $chat = Chat::factory()->private()->create();
        $chat->members()->createMany([
            ['user_id' => $user->id],
            ['user_id' => $recipient->id],
        ]);
        $chat->update(['title' => 'My Private Chat']);

        Message::factory()->create([
            'chat_id' => $chat->id,
            'sender_id' => $user->id,
            'body' => 'Hello Jane!',
        ]);

        $response = $this->withHeaders([
            'Authorization' => "Bearer $token"
        ])->getJson('/api/search?q=Jane');

        $response->assertOk();

        $response->assertJsonStructure([
            'chats',
            'global_search' => [['title', 'description', 'redirect_url']],
            'messages' => [['title', 'description', 'redirect_url', 'message']],
        ]);
    }

    public function test_search_requires_query_param()
    {
        $user = User::factory()->create();
        $token = $this->authenticate($user);

        $response = $this->withHeaders([
            'Authorization' => "Bearer $token"
        ])->getJson('/api/search');

        $response->assertStatus(400);
    }
}
