<?php

namespace App\Events\Auth\Sessions;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Session;

class SessionTerminated
{
    use Dispatchable, SerializesModels;

    public int $userId;
    public string $sessionId;

    public function __construct(Session $session)
    {
        $this->userId = $session->user_id;
        $this->sessionId = (string) $session->id;
    }
}
