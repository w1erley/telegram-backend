<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\VerificationCode;
use Carbon\Carbon;

class VerifyEmailTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_verify_email_with_correct_code()
    {
        $user = User::factory()->create(['email_verified_at' => null]);

        VerificationCode::factory()->create([
            'user_id' => $user->id,
            'code' => 'ABC123',
            'expires_at' => Carbon::now()->addMinutes(10),
        ]);

        $response = $this->getJson('/api/verify-email/ABC123');

        $response->assertStatus(200);
        $response->assertJson(['message' => 'Email verified successfully']);
        $this->assertNotNull($user->fresh()->email_verified_at);
    }

    public function test_verification_fails_with_invalid_code()
    {
        $user = User::factory()->create();

        VerificationCode::factory()->create([
            'user_id' => $user->id,
            'code' => 'ABC123',
            'expires_at' => Carbon::now()->addMinutes(10),
        ]);

        $response = $this->getJson('/api/verify-email/WRONGCODE');

        $response->assertStatus(400);
        $response->assertJson(['message' => 'Invalid verification code']);
    }
}
