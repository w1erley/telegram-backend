<?php

namespace App\Events\Message;

class MessageEdited extends AbstractMessageEvent
{
    public $event = 'message.edited';

    public function broadcastAs(): string
    {
        return 'message.edited';
    }
}
