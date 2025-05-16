<?php

namespace App\Events\Message;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Queue\SerializesModels;

abstract class AbstractMessageEvent implements ShouldBroadcastNow
{
    use SerializesModels;

    public function __construct(protected array $payload, protected int $chatId) {}

    public function broadcastOn(): array
    {
        return [new PrivateChannel('private-chat.'.$this->chatId)];
    }

    public function broadcastWith(): array
    {
        return $this->payload;
    }
}
