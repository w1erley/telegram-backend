<?php

namespace App\Events\Message;

class MessageDeleted extends AbstractMessageEvent
{
    public $event = 'message.deleted';
}
