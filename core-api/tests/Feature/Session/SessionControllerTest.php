<?php

namespace Tests\Feature\Session;

use App\Models\Session;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SessionControllerTest extends TestCase
{
    use RefreshDatabase;
    public function test_index_returns_sessions()
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token');

        Session::factory()->create([
            'user_id' => $user->id,
            'personal_access_token_id' => $token->accessToken->id,
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token->plainTextToken,
        ])->getJson('/api/sessions');

        $response->assertOk();
        $response->assertJsonStructure([
            'current',
            'others',
        ]);
    }

    public function test_destroy_current_session()
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token');

        Session::factory()->create([
            'user_id' => $user->id,
            'personal_access_token_id' => $token->accessToken->id,
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token->plainTextToken,
        ])->postJson('/api/logout');

        $response->assertOk();
        $response->assertJson(['message' => 'Session terminated (logged out)']);
    }

    public function test_destroy_other_session()
    {
        $user = User::factory()->create();
        $currentToken = $user->createToken('test-token-1');
        $otherToken = $user->createToken('test-token-2');

        $currentSession = Session::factory()->create([
            'user_id' => $user->id,
            'personal_access_token_id' => $currentToken->accessToken->id,
        ]);

        $otherSession = Session::factory()->create([
            'user_id' => $user->id,
            'personal_access_token_id' => $otherToken->accessToken->id,
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $currentToken->plainTextToken,
        ])->deleteJson("/api/sessions/{$otherSession->id}");

        $response->assertOk();
        $response->assertJson(['message' => 'Session terminated successfully']);
    }

    public function test_destroy_other_sessions()
    {
        $user = User::factory()->create();
        $currentToken = $user->createToken('current-token');

        $currentSession = Session::factory()->create([
            'user_id' => $user->id,
            'personal_access_token_id' => $currentToken->accessToken->id,
        ]);

        for ($i = 0; $i < 2; $i++) {
            $token = $user->createToken('other-token-' . $i);
            Session::factory()->create([
                'user_id' => $user->id,
                'personal_access_token_id' => $token->accessToken->id,
            ]);
        }

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $currentToken->plainTextToken,
        ])->deleteJson('/api/sessions/others');

        $response->assertOk();
        $response->assertJson(['message' => 'All other sessions terminated']);
    }
}