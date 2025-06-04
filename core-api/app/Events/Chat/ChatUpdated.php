<?php

namespace App\Events\Chat;


class ChatUpdated extends AbstractChatEvent
{
    public function broadcastAs(): string
    {
        return 'chat.updated';
    }
}