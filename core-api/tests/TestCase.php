<?php

namespace Tests;

use App\Models\Session;
use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Mail;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Mail::fake();
    }

    protected function authenticate(User $user): string {
        $token = $user->createToken('test-token');

        Session::factory()->create([
            'user_id' => $user->id,
            'personal_access_token_id' => $token->accessToken->id,
        ]);

        return $token->plainTextToken;
    }
}
