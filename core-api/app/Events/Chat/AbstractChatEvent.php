<?php

namespace App\Events\Chat;

use App\Http\Resources\ChatResource;
use App\Models\Chat;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Queue\SerializesModels;

abstract class AbstractChatEvent implements ShouldBroadcastNow
{
    use SerializesModels;

    public function __construct(protected Chat $chat, protected int $userId)
    {
        $this->chat->loadMissing('members.user', 'lastMessage');
    }

    public function broadcastOn() {
        return new PrivateChannel("user-chats.{$this->userId}");
    }

    public function broadcastWith() {
        return (new ChatResource($this->chat, $this->userId))->resolve();
    }
}
