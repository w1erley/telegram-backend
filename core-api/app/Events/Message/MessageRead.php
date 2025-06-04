<?php

namespace App\Events\Message;

use App\Models\Message;

class MessageRead extends AbstractMessageEvent
{

    protected int $readerId;

    public $event = 'message.read';

    public function __construct(Message $message, int $readerId)
    {
        parent::__construct($message);
        $this->readerId = $readerId;
    }

    public function broadcastAs(): string
    {
        return 'message.read';
    }

    public function broadcastWith(): array
    {
        $payload = parent::broadcastWith();
        $payload['reader_id'] = $this->readerId;
        return $payload;
    }
}
