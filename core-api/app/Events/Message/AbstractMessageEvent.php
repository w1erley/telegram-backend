<?php

namespace App\Events\Message;

use App\Http\Resources\MessageResource;
use App\Models\Message;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Queue\SerializesModels;

abstract class AbstractMessageEvent implements ShouldBroadcastNow
{
    use SerializesModels;

    public function __construct(protected Message $message)
    {
        $this->message->loadMissing('sender', 'chat.members');
    }

    public function broadcastOn(): array
    {
        $channels = [];
        foreach ($this->message->chat->members as $member) {
            $channels[] = new PrivateChannel('user-chats.' . $member->user_id);
        }

        return $channels;
    }

    public function broadcastWith(): array
    {
        return (new MessageResource($this->message))->resolve();
    }
}
