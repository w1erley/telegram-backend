<?php

namespace App\Services\Web\Auth;

use App\Events\Auth\Manual\UserLoggedIn;
use App\Mail\VerificationEmail;
use App\Models\User;
use App\Repositories\Web\VerificationCodeRepository;
use App\Services\BaseService;
use App\Services\Web\UserService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class VerificationService extends BaseService
{
    public function __construct(
        private readonly VerificationCodeRepository $verificationCodeRepository,
        private readonly UserService $userService,
    )
    {
        parent::__construct($verificationCodeRepository);
    }

    public function sendVerificationEmail(User $user)
    {
        Mail::fake();

        $this->deleteBy(['user_id' => $user->id]);

        $verificationCode = $this->verificationCodeRepository->generateForUser($user);

        Mail::to($user->email)->send(new VerificationEmail($user, $verificationCode->code));
    }

    public function verifyEmail(string $code)
    {
        $verification = $this->findBy(['code' => $code]);

        if (!$verification) {
            return response()->json(['message' => 'Invalid verification code'], 400);
        }

        if ($verification->isExpired()) {
            return response()->json(['message' => 'Verification code expired'], 400);
        }

        $this->userService->update($verification->user->id, ['email_verified_at' => now()]);
        $this->delete($verification->id);

        return response()->json(['message' => 'Email verified successfully']);
    }
}
