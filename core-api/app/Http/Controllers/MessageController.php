<?php

namespace App\Http\Controllers;

use Dotenv\Exception\ValidationException;
use Illuminate\Http\Request;
use App\Http\Requests\Message\{
    StoreMessageRequest, UpdateMessageRequest, ReactRequest
};
use App\Models\{Message, Chat};
use App\Services\Web\MessageService;

class MessageController extends Controller
{
    public function __construct(private MessageService $svc) {}

    public function index(Request $request, Chat $chat)
    {
        abort_if(!$chat->members()->where('user_id', auth()->id())->exists(), 403);

        $data = $request->validate([
            'before' => 'nullable|integer|min:1',
            'after'  => 'nullable|integer|min:1',
            'limit'  => 'nullable|integer|min:1|max:100',
        ]);

        if (!empty($data['before']) && !empty($data['after'])) {
            throw ValidationException::withMessages([
                'before' => 'Use either "before" or "after", not both.',
                'after'  => 'Use either "before" or "after", not both.',
            ]);
        }

        $messages = $this->svc->fetchHistory(
            $chat,
            before: $data['before'] ?? null,
            after : $data['after']  ?? null,
            limit : $data['limit']  ?? 20
        );

        return response()->json($messages);
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

    public function markRead(Chat $chat, Message $message)
    {
        $this->svc->markReadUntil($chat, $message);
        return response()->json(['ok' => true]);
    }
}
