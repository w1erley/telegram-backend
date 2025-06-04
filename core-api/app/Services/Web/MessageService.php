<?php

namespace App\Services\Web;

use App\Events\Chat\ChatUpdated;
use App\Services\BaseService;
use App\Models\{Chat, Message, MessageStat};
use App\Repositories\Web\MessageRepository;
use Illuminate\Support\Facades\DB;
use App\Events\Message\{MessageRead, MessageSent, MessageEdited, MessageDeleted, ReactionUpdated};

class MessageService extends BaseService
{
    public function __construct(MessageRepository $repo)
    {
        parent::__construct($repo);
    }

    public function fetchHistory(
        Chat $chat,
        ?int $before = null,
        ?int $after  = null,
        int  $limit  = 20
    ) {
        return $this->repository->fetchHistory(
            chatId : $chat->id,
            beforeId: $before,
            afterId : $after,
            limit   : $limit
        );
    }

    public function send(int $chatId, string $body, ?int $replyId = null): Message
    {
        $msg = DB::transaction(function() use ($chatId, $body, $replyId) {
            $message = Message::create([
                'chat_id'       => $chatId,
                'sender_id'     => auth()->id(),
                'type'          => 'plain',
                'body'          => $body,
                'reply_to_id'   => $replyId,
                'thread_root_id'=> $replyId ?: null,
            ]);

            MessageStat::create([
                'message_id' => $message->id,
                'user_id'    => auth()->id(),
                'read_at'    => now(),
            ]);

            return $message;
        });

        $msg->load('chat', 'chat.members');

        broadcast(new MessageSent($msg))->toOthers();
        foreach ($msg->chat->members as $member) {
            event(new ChatUpdated($msg->chat, $member->user_id));
        }

        return $msg;
    }

    public function edit(Message $msg, string $body): Message
    {
        $msg->update(['body' => $body, 'edited_at' => now()]);

        broadcast(new MessageEdited($msg))->toOthers();
        foreach ($msg->chat->members as $member) {
            event(new ChatUpdated($msg->chat, $member->user_id));
        }

        return $msg;
    }

    public function destroy(Message $msg): void
    {
        $msg->delete();
        broadcast(new MessageDeleted($msg))->toOthers();
        foreach ($msg->chat->members as $member) {
            event(new ChatUpdated($msg->chat, $member->user_id));
        }
    }

//    public function react(Message $msg, string $emoji): void
//    {
//        MessageStat::updateOrCreate(
//            ['message_id' => $msg->id, 'user_id' => auth()->id()],
//            ['reaction'   => $emoji,     'reacted_at' => now()]
//        );
//        broadcast(new ReactionUpdated($msg->id, auth()->id(), $emoji))->toOthers();
//    }

//    protected function assertCanSend(int $chatId): void
//    {
//        $member = Chat::findOrFail($chatId)
//            ->members()->where('user_id', auth()->id())->first();
//
//        abort_if(!$member, 403);
//        if ($member->role === 'member' && $member->is_muted) {
//            abort(403, 'Muted');
//        }
//    }
//
//    protected function assertSenderOrAdmin(Message $msg): void
//    {
//        if ($msg->sender_id === auth()->id()) return;
//
//        $member = $msg->chat
//            ->members()->where('user_id', auth()->id())->first();
//
//        abort_if(!$member || !in_array($member->role, ['owner','admin']), 403);
//    }

    public function markReadUntil(Chat $chat, Message $msg): void
    {
        $chat->load('messages');

        $userId = auth()->id();

        $ids = $chat->messages()
            ->where('id', '<=', $msg->id)
            ->pluck('id')
            ->all();

        if (empty($ids)) {
            return;
        }

        $existing = MessageStat::query()
            ->where('user_id', $userId)
            ->whereIn('message_id', $ids)
            ->get(['message_id', 'read_at'])
            ->keyBy('message_id');

        $toInsert = [];
        $toUpdate = [];

        foreach ($ids as $id) {
            if (!isset($existing[$id])) {
                $toInsert[] = [
                    'message_id' => $id,
                    'user_id'    => $userId,
                    'read_at'    => now(),
                    'reaction'   => '',
                    'reacted_at' => null,
                ];
            } elseif ($existing[$id]->read_at === null) {
                $toUpdate[] = $id;
            }
        }

        if (!empty($toInsert)) {
            MessageStat::insertOrIgnore($toInsert);
        }

        if (!empty($toUpdate)) {
            MessageStat::query()
                ->where('user_id', $userId)
                ->whereIn('message_id', $toUpdate)
                ->whereNull('read_at')
                ->update(['read_at' => now()]);
        }

        $msg->load('stats.user');

        broadcast(new MessageRead($msg, $userId))->toOthers();

        foreach ($chat->members as $member) {
            event(new ChatUpdated($chat, $member->user_id));
        }
    }

}
