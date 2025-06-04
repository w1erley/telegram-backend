<?php

namespace App\Events\Chat;


class ChatCreated extends AbstractChatEvent
{
    public function broadcastAs(): string
    {
        return 'chat.created';
    }
}