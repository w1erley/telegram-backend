<?php

namespace App\Events\Message;


class MessageSent extends AbstractMessageEvent
{
    public function broadcastAs(): string
    {
        return 'message.sent';
    }
}