<?php

namespace App\Http\Controllers;

use App\Http\Requests\Message\StartPrivateChatRequest;
use App\Http\Requests\SearchRequest;
use App\Http\Resources\ChatResource;
use App\Models\Chat;
use App\Services\Web\ChatService;
use App\Services\Web\MessageService;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function __construct(
        private ChatService $svc,
        private MessageService $messageService,
    ) {}

    public function index()
    {
        return response()->json(
            $this->svc->listForUser(auth()->id())
        );
    }

    public function store(Request $r)
    {
        $data = $r->validate([
            'type'  => 'required|in:group,channel',
            'title' => 'required|string|max:255',
            'about' => 'nullable|string|max:1000'
        ]);

        $chat = $data['type'] === 'group'
            ? $this->svc->createGroup($data['title'], $data['about'])
            : $this->svc->createChannel($data['title'], $data['about']);

        return new ChatResource($chat);
    }

    public function update(Chat $chat, UpdateChatRequest $r)
    {
        abort_if(auth()->id() !== $chat->owner_id, 403, 'Only owner can update');

        $updated = $this->svc->update($chat->id, $r->validated());
        return new ChatResource($updated);
    }

    public function destroy(Chat $chat)
    {
        abort_if(auth()->id() !== $chat->owner_id, 403, 'Only owner can delete');

        $this->svc->delete($chat->id);
        return response()->json(['ok' => true]);
    }

//    public function createPrivate(int $userId)
//    {
//        return response()->json(
//            $this->svc->createPrivate(auth()->id(), $userId)
//        );
//    }

    public function show(string $key)
    {
        $chat = $this->svc->getByKey($key);

        if ($chat instanceof Chat) {
            abort_if(
                !$chat->members()->where('user_id', auth()->id())->exists(),
                403,
                'Forbidden'
            );

            return response()->json(new ChatResource($chat));
        }

        return $chat;
    }

    public function createPrivateChatAndSend(int $recipient, StartPrivateChatRequest $r)
    {
        $validated = $r->validated();

        $chat = $this->svc->createPrivate($recipient, auth()->id());
        $this->messageService->send($chat->id, $validated['body']);

        return response()->json(new ChatResource($chat));
    }
}
