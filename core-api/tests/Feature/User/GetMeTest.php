<?php

namespace Tests\Feature\User;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GetMeTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_get_their_info()
    {
        $user = User::factory()->create();
        $token = $this->authenticate($user);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/user/me');

        $response->assertOk()
            ->assertJson([
                'id' => $user->id,
                'email' => $user->email,
            ]);
    }

    public function test_guest_cannot_access_user_me_route()
    {
        $response = $this->getJson('/api/user/me');

        $response->assertUnauthorized();
    }
}
