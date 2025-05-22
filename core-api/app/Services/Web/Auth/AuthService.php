<?php

namespace App\Services\Web\Auth;

use App\Events\Auth\Manual\UserLoggedIn;
use App\Http\Requests\RegisterRequest;
use App\Mail\VerificationEmail;
use App\Models\User;
use App\Services\Web\UserService;
use App\Traits\Loggable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class AuthService
{
    use Loggable;
    public function __construct(
        private readonly UserService $userService,
        private readonly VerificationService $verificationService,
        private readonly SessionService $sessionService
    ) {}

    public function register(array $credentials)
    {
        DB::beginTransaction();

        try {
            Log::info("registering", ["credentials" => $credentials]);
            $user = $this->userService->store($credentials);

            DB::commit();

            return ['user' => $user];
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->error($e->getMessage());
            throw $e;
        }
    }

    public function login(array $credentials)
    {
        if (!Auth::attempt($credentials)) {
            throw ValidationException::withMessages([
                'identifier' => ['Invalid name/email or password.'],
                'password' => ['Invalid name/email or password.'],
            ]);
        }

        $user = Auth::user();
        if (!$user instanceof User) {
            throw new \RuntimeException('Authenticated user is not a valid User model instance.');
        }
//
//        if (!$user->email_verified_at) {
//            $this->verificationService->sendVerificationEmail($user);
//            throw ValidationException::withMessages([
//                'email' => ['Your email is not verified. Please check your inbox.'],
//            ]);
//        }

        ['session' => $session, 'token' => $token] = $this->sessionService->createSession($user);

        $message = ['id' => 1];
        event(new UserLoggedIn($message));

        return [
            'user' => $user,
            'session' => $session,
            'token' => $token,
        ];
    }

    public function logout(string $authToken)
    {
        return $this->sessionService->terminateSession($authToken);
    }

    public function logoutAllSessions(int $userId)
    {
        return $this->sessionService->terminateAllSessions($userId);
    }
}
