<?php

namespace App\Listeners\Auth\Manual;

use App\Events\Auth\Manual\UserLoggedIn;
use Illuminate\Support\Facades\Redis;

class NotifyUserLogin
{
    public function __construct()
    {
        //
    }

    public function handle(UserLoggedIn $event)
    {
        $messageId = $event->message['id'];

        $payload = json_encode([
            'type' => 'message',
            'message_id' => $messageId,
        ]);

        Redis::publish('notifications:new_message', $payload);
    }
}
