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
        abort_if(!$chat->members()->where('user_id', auth()->id())->exists(), 403);
        $after = request('after');
        return response()->json(
            $this->svc->fetchHistory($chat, $after)
        );
    }

    public function store(Chat $chat, StoreMessageRequest $r)
    {
        abort_if(!$chat->members()->where('user_id', auth()->id())->exists(), 403);

        $msg = $this->svc->send(
            $chat->id,
            $r->body,
            $r->reply_to_id
        );
        return response()->json($msg);
    }

    public function update(
        Chat $chat,
        Message $message,
        UpdateMessageRequest $r
    ) {
        abort_if($message->chat_id !== $chat->id, 404);

        $isSender = $message->sender_id === auth()->id();
        $isAdmin  = $chat->members()
            ->where('user_id', auth()->id())
            ->whereIn('role', ['owner','admin'])
            ->exists();

        abort_if(!$isSender && !$isAdmin, 403);

        return response()->json(
            $this->svc->edit($message, $r->body)
        );
    }

    public function destroy(Chat $chat, Message $message)
    {
        abort_if($message->chat_id !== $chat->id, 404);

        $isSender = $message->sender_id === auth()->id();
        $isAdmin  = $chat->members()
            ->where('user_id', auth()->id())
            ->whereIn('role', ['owner','admin'])
            ->exists();

        abort_if(!$isSender && !$isAdmin, 403);

        $this->svc->destroy($message);
        return response()->json(['ok' => true]);
    }

    public function react(
        Chat $chat,
        Message $message,
        ReactRequest $r
    ) {
        abort_if($message->chat_id !== $chat->id, 404);
        abort_if(
            !$chat->members()->where('user_id', auth()->id())->exists(),
            403
        );

        $this->svc->react($message, $r->emoji);
        return response()->json(['ok' => true]);
    }

    public function markRead(
        Chat $chat,
        Message $message
    )
    {
        $this->svc->markRead($message->id);
        return response()->json(['ok' => true]);
    }
}
