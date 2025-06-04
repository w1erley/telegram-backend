<?php

namespace App\Http\Controllers;

use App\Services\Web\Auth\SessionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;

class SessionController extends Controller
{
    public function __construct(
        private readonly SessionService $sessionService
    ) {}

    public function index(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $token = $request->bearerToken();
        $accessToken = PersonalAccessToken::findToken($token);

        $currentTokenId = $accessToken?->id;

        $sessions = $this->sessionService->getAllSessionsForUser($user, $currentTokenId);

        return response()->json($sessions);
    }

    public function destroy(Request $request, int $id)
    {
        $user = $request->user();
        if (! $user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $result = $this->sessionService->terminateSessionById($id, $user);
        if (! $result) {
            return response()->json([
                'message' => 'Failed to terminate session (maybe it is current or not found).'
            ], 400);
        }

        return response()->json(['message' => 'Session terminated successfully']);
    }

    public function destroyOthers(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $token = $request->bearerToken();
        $accessToken = PersonalAccessToken::findToken($token);

        $currentTokenId = $accessToken?->id;

        $this->sessionService->terminateOtherSessions($user, $currentTokenId);

        return response()->json(['message' => 'All other sessions terminated']);
    }
}
