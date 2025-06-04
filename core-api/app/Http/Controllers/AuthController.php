<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Services\Web\Auth\AuthService;
use App\Services\Web\Auth\VerificationService;
use App\Services\Web\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function __construct(
        private readonly VerificationService $verificationService,
        private readonly AuthService $authService
    ) {}

    public function verifyEmail($code)
    {
        return $this->verificationService->verifyEmail($code);
    }

    public function register(RegisterRequest $request)
    {
        try {
            $credentials = $request->validated();
            $user = $this->authService->register($credentials);

            return $this->ok(['user' => $user], 201);
        } catch (\Exception $e) {
            return $this->fail($e->getMessage(), 403);
        }
    }

    public function login(LoginRequest $request)
    {
        try {
            $credentials = $request->validated();

            $authData = $this->authService->login($credentials);

            return $this->ok([
                'user' => $authData['user'],
                'session' => $authData['session'],
                'token' => $authData['token']
            ], 201);
        } catch (\Exception $e) {
            return $this->fail($e->getMessage(), 403);
        }
    }
}
