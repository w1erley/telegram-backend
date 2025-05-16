<?php

namespace App\Services\Web\Auth;

use App\Models\User;
use App\Repositories\Web\SessionRepository;
use App\Models\Session;
use App\Services\BaseService;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Laravel\Sanctum\PersonalAccessToken;

class SessionService extends BaseService
{
    public function __construct(
        private readonly SessionRepository $sessionRepository
    )
    {
        parent::__construct($sessionRepository);
    }

    public function createSession(User $user)
    {
        $personalAccessToken = $user->createToken('auth_token', expiresAt: Carbon::now()->addHours(2));

        $session = $this->store([
            'user_id' => $user->id,
            'personal_access_token_id' => $personalAccessToken->accessToken->id,
            'device' => request()->header('User-Agent'),
            'ip_address' => request()->ip(),
            'browser' => request()->header('User-Agent'),
            'last_active_at' => now(),
        ]);

        return [
            "session" => $session,
            "token" => $personalAccessToken
        ];
    }

    public function terminateSession($authToken)
    {
        $token = PersonalAccessToken::findToken($authToken);
        if ($token) {
            $session = $this->sessionRepository->findBy(['personal_access_token_id' => $token->id]);
            if ($session) {
                $session->delete();
            } else {
                $token->delete();
            }
        }

        return response()->json(['message' => 'Logged out successfully']);
    }

    public function terminateAllSessions($userId)
    {
        $this->repository->deleteBy(['user_id' => $userId]);

        return response()->json(['message' => 'Logged out from all devices']);
    }
}
