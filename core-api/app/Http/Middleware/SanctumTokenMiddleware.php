<?php

namespace App\Http\Middleware;

use Closure;
use Carbon\Carbon;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use Laravel\Sanctum\PersonalAccessToken;

use App\Models\Session;

class SanctumTokenMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json(['message' => 'Token required'], 401);
        }

        $accessToken = PersonalAccessToken::findToken($token);
        if (!$accessToken) {
            return response()->json(['message' => 'Token not found'], 401);
        }

        if ($accessToken->expires_at && Carbon::now()->gt($accessToken->expires_at)) {
            return response()->json(['message' => 'Session expired'], 401);
        }

        $user = $accessToken->tokenable;
        if (!$user) {
            return response()->json(['message' => 'Invalid user'], 401);
        }

        Auth::setUser($user);

        $session = Session::where('personal_access_token_id', $accessToken->id)->first();
        if (!$session) {
            return response()->json(['message' => 'Invalid session'], 401);
        }

        $session->update(['last_active_at' => Carbon::now()]);

        $newExpiration = Carbon::now()->addMinutes(config('sanctum.expiration'));
        $accessToken->forceFill(['expires_at' => $newExpiration])->save();

        return $next($request);
    }
}
