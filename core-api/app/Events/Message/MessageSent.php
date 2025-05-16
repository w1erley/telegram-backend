<?php

namespace App\Events\Message;

class MessageSent extends AbstractMessageEvent
{
    public $event = 'message.sent';
}
