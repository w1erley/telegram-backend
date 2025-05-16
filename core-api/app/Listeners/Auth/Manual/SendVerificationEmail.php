<?php

namespace App\Listeners\Auth\Manual;

use App\Events\Auth\Manual\UserRegistered;
use App\Services\Web\Auth\VerificationService;

class SendVerificationEmail
{
    public function __construct(
        private readonly VerificationService $verificationService
    ) {}

    public function handle(UserRegistered $event)
    {
        $this->verificationService->sendVerificationEmail($event->user);
    }
}
