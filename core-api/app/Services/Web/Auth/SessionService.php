<?php

namespace App\Services\Web\Auth;

use App\Models\User;
use App\Repositories\Web\SessionRepository;
use App\Models\Session;
use App\Services\BaseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Laravel\Sanctum\PersonalAccessToken;
use Carbon\Carbon;

class SessionService extends BaseService
{
    public function __construct(
        private readonly SessionRepository $sessionRepository
    ) {
        parent::__construct($sessionRepository);
    }

    public function createSession(User $user): array
    {
        $personalAccessToken = $user
            ->createToken('auth_token', expiresAt: Carbon::now()->addHours(2));

        $session = $this->sessionRepository->store([
            'user_id' => $user->id,
            'personal_access_token_id' => $personalAccessToken->accessToken->id,
            'device' => request()->header('User-Agent'),
            'ip_address' => request()->ip(),
            'browser' => request()->header('User-Agent'),
            'last_active_at' => now(),
        ]);

        return [
            'session' => $session,
            'token' => $personalAccessToken,
        ];
    }

    public function getAllSessionsForUser(User $user, int $currentTokenId): array
    {
        $allSessions = $this->sessionRepository->findCoupleBy(['user_id' => $user->id]);

        Log::info("test", ["currentTokenId" => $currentTokenId]);

        if (!is_iterable($allSessions)) {
            return [
                'current' => null,
                'others'  => [],
            ];
        }

        $current = null;
        $others = [];

        foreach ($allSessions as $session) {
            if (!($session instanceof Session)) {
                continue;
            }

            if ($session->personal_access_token_id === $currentTokenId) {
                $current = $session;
            } else {
                $others[] = $session;
            }
        }

        return [
            'current' => $current,
            'others'  => $others,
        ];
    }

    public function terminateCurrentSession(User $user, int $currentTokenId): void
    {
        $session = $this->sessionRepository->findBy([
            'user_id' => $user->id,
            'personal_access_token_id' => $currentTokenId,
        ]);

        if ($session) {
            $session->delete();
        }
    }

    public function terminateSessionById(int $sessionId, User $user): bool
    {
        $session = $this->sessionRepository->one($sessionId);

        if (!($session instanceof Session) || $session->user_id !== $user->id) {
            return false;
        }

        $currentToken = $user->currentAccessToken();
        if ($session->personal_access_token_id === $currentToken?->id) {
            return false;
        }

//        $token = PersonalAccessToken::find($session->personal_access_token_id);
//        if ($token) {
//            $token->delete();
//        }

        $session->delete();

        return true;
    }

    public function terminateOtherSessions(User $user, int $currentTokenId): void
    {
        $allSessions = $this->sessionRepository->findCoupleBy([
            'user_id' => $user->id,
        ]);

        if (!is_iterable($allSessions)) {
            return;
        }

        foreach ($allSessions as $session) {
            if (!($session instanceof Session)) {
                continue;
            }

            if ($session->personal_access_token_id !== $currentTokenId) {
//                $token = PersonalAccessToken::find($session->personal_access_token_id);
//                if ($token) {
//                    $token->delete();
//                }
                $session->delete();
            }
        }
    }
}
