<?php

namespace App\Repositories\Web;

use App\Models\VerificationCode;
use App\Repositories\BaseRepository;
use Carbon\Carbon;
use Illuminate\Support\Str;

class VerificationCodeRepository extends BaseRepository
{
    public function __construct(VerificationCode $verificationCode)
    {
        parent::__construct(
            $verificationCode,
            'verification_code',
        );
    }

    public function generateForUser($user)
    {
        return $this->store([
            'user_id' => $user->id,
            'code' => Str::random(40),
            'expires_at' => Carbon::now()->addMinutes(10),
        ]);
    }
}
