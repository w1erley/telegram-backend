<?php

namespace App\Events\Message;

use App\Http\Resources\MessageResource;
use App\Models\Message;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

abstract class AbstractMessageEvent implements ShouldBroadcastNow
{
    use SerializesModels;

    public function __construct(protected Message $message) {}

    public function broadcastOn(): array
    {
        return [new PrivateChannel('private-chat.'.$this->message->chat_id)];
    }

    public function broadcastWith(): array
    {
        $resolved = (new MessageResource($this->message))->resolve();

        Log::info('📤 Broadcasting payload', [
            'chat_id' => $this->message->chat_id,
            'payload' => $resolved,
        ]);

        return $resolved;
    }
}
