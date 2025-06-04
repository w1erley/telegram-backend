<?php

namespace App\Events\Message;

class MessageDeleted extends AbstractMessageEvent
{
    public $event = 'message.deleted';

    public function broadcastAs(): string
    {
        return 'message.deleted';
    }
}
