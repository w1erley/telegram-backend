<?php

namespace App\Http\Controllers;

use App\Http\Requests\Message\{
    StoreMessageRequest, UpdateMessageRequest, ReactRequest
};
use App\Models\{Message, Chat};
use App\Services\Web\MessageService;

class MessageController extends Controller
{
    public function __construct(private MessageService $svc) {}

    public function index(Chat $chat)
    {
        $after = request('after');
        return response()->json(
            $this->svc->fetchHistory($chat->id, $after)
        );
    }

    public function store(StoreMessageRequest $r)
    {
        $msg = $this->svc->send(
            $r->chat_id,
            $r->body,
            $r->reply_to_id
        );
        return response()->json($msg);
    }

    public function update(Message $message, UpdateMessageRequest $r)
    {
        return response()->json(
            $this->svc->edit($message, $r->body)
        );
    }

    public function destroy(Message $message)
    {
        $this->svc->destroy($message);
        return response()->json(['ok' => true]);
    }

    public function react(Message $message, ReactRequest $r)
    {
        $this->svc->react($message, $r->emoji);
        return response()->json(['ok' => true]);
    }

    public function markRead(Message $message)
    {
        $this->svc->markRead($message->id);
        return response()->json(['ok' => true]);
    }
}
