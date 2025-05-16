<?php

namespace App\Events\Message;

class ReactionUpdated extends AbstractMessageEvent
{
    public $event = 'reaction.updated';
}
