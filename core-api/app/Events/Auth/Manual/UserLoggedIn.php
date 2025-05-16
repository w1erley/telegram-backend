<?php

namespace App\Events\Auth\Manual;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class UserLoggedIn
{
    use Dispatchable, SerializesModels;

    public mixed $message;

    public function __construct(mixed $message)
    {
        $this->message = $message;
    }
}
