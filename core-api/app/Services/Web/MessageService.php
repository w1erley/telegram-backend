<?php

namespace App\Services\Web;

use App\Services\BaseService;
use App\Models\{Chat, Message, MessageStat};
use App\Repositories\Web\MessageRepository;
use Illuminate\Support\Facades\DB;
use App\Events\Message\{
    MessageSent, MessageEdited, MessageDeleted, ReactionUpdated
};

class MessageService extends BaseService
{
    public function __construct(MessageRepository $repo)
    {
        parent::__construct($repo);
    }

    public function fetchHistory(Chat $chat, int $after) {
        return $this->repository->fetchHistory($chat->id, $after);
    }

    public function send(int $chatId, string $body, ?int $replyId = null): Message
    {
        $this->assertCanSend($chatId);

        $msg = DB::transaction(fn () =>
        Message::create([
            'chat_id'       => $chatId,
            'sender_id'     => auth()->id(),
            'type'          => 'plain',
            'body'          => $body,
            'reply_to_id'   => $replyId,
            'thread_root_id'=> $replyId ?: null,
        ])
        );

        broadcast(new MessageSent($msg))->toOthers();
        return $msg;
    }

    public function edit(Message $msg, string $body): Message
    {
        $this->assertSenderOrAdmin($msg);
        $msg->update(['body' => $body, 'edited_at' => now()]);
        broadcast(new MessageEdited($msg))->toOthers();
        return $msg;
    }

    public function destroy(Message $msg): void
    {
        $this->assertSenderOrAdmin($msg);
        $msg->update(['deleted_at' => now()]);
        broadcast(new MessageDeleted($msg->id, $msg->chat_id))->toOthers();
    }

    public function react(Message $msg, string $emoji): void
    {
        MessageStat::updateOrCreate(
            ['message_id' => $msg->id, 'user_id' => auth()->id()],
            ['reaction'   => $emoji,     'reacted_at' => now()]
        );
        broadcast(new ReactionUpdated($msg->id, auth()->id(), $emoji))->toOthers();
    }


    public function markRead(int $lastMessageId): void
    {
        MessageStat::updateOrCreate(
            ['message_id' => $lastMessageId, 'user_id' => auth()->id()],
            ['read_at' => now()]
        );
        // broadcast
    }

    protected function assertCanSend(int $chatId): void
    {
        $member = Chat::findOrFail($chatId)
            ->members()->where('user_id', auth()->id())->first();

        abort_if(!$member, 403);
        if ($member->role === 'member' && $member->is_muted) {
            abort(403, 'Muted');
        }
    }

    protected function assertSenderOrAdmin(Message $msg): void
    {
        if ($msg->sender_id === auth()->id()) return;

        $member = $msg->chat
            ->members()->where('user_id', auth()->id())->first();

        abort_if(!$member || !in_array($member->role, ['owner','admin']), 403);
    }
}
