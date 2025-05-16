<?php

namespace App\Listeners\Auth\Sessions;

use App\Events\Auth\Sessions\SessionTerminated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Broadcast;

class NotifySessionTermination implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(SessionTerminated $event)
    {
        Broadcast::channel('user.' . $event->userId, function () use ($event) {
            return [
                'message' => 'Session terminated',
                'session_id' => $event->sessionId,
                'user_id' => $event->userId
            ];
        });

        Log::info("Session {$event->sessionId} terminated for user {$event->userId}");
    }
}
