<?php

namespace App\Services\Web;

use App\Models\Message;

class ThreadService
{
    public function sendComment(Message $rootPost, string $body): Message
    {
        abort_if($rootPost->chat->type !== 'channel', 400);

        return app(MessageService::class)->send(
            chatId:  $rootPost->chat_id,
            body:    $body,
            replyId: $rootPost->id
        );
    }

    public function history(Message $rootPost, int $after = null, int $limit = 50)
    {
        return Message::where('thread_root_id', $rootPost->id)
            ->when($after, fn($q) => $q->where('id','>', $after))
            ->orderBy('id')
            ->take($limit)
            ->get();
    }

    public function count(Message $rootPost): int
    {
        return Message::where('thread_root_id', $rootPost->id)->count();
    }
}
