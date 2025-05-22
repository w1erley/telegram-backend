<?php

namespace App\Services\Web;

use App\Http\Resources\ChatResource;
use App\Services\BaseService;
use App\Models\{Chat, ChatMember, User};
use App\Repositories\Web\ChatRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ChatService extends BaseService
{
    public function __construct(ChatRepository $repo)
    {
        parent::__construct($repo);
    }

    public function listForUser(int $userId)
    {
        return ChatResource::collection(
            $this->repository->listForUserWithMeta($userId)
        );
    }

    public function getByKey(string $key): Chat
    {
        if (ctype_digit($key)) {
            return $this->repository->one((int)$key, ['members.user']);
        }

        $username = ltrim($key, '@');

        $chat = Chat::whereIn('type', ['group','channel'])
            ->where('username', $username)
            ->with('members.user')
            ->first();

        if ($chat) {
            return $chat;
        }

        return $this->findOrCreatePrivateByUsername($username);
    }

    public function findOrCreatePrivateByUsername(string $username): Chat
    {
        $me = auth()->id();
        $other = User::where('username', $username)->firstOrFail();

        return DB::transaction(function () use ($me, $other) {
            return $this->createPrivate($me, $other->id);
        });
    }

    public function createPrivate(int $userA, int $userB): Chat
    {
        if ($chat = $this->repository->findPrivateBetween($userA, $userB)) {
            return $chat;
        }

        return DB::transaction(function () use ($userA, $userB) {
            $chat = Chat::create([
                'type'     => 'private',
                'owner_id' => $userA,
            ]);

            ChatMember::insert([
                ['chat_id' => $chat->id, 'user_id' => $userA, 'role' => 'owner'],
                ['chat_id' => $chat->id, 'user_id' => $userB, 'role' => 'member'],
            ]);

            // broadcast
            return $chat;
        });
    }

    public function createGroup(string $title, ?string $about = null): Chat
    {
        return DB::transaction(function () use ($title, $about) {
            $chat = Chat::create([
                'type'     => 'group',
                'title'    => $title,
                'about'    => $about,
                'owner_id' => auth()->id(),
            ]);

            $chat->members()->create([
                'user_id' => auth()->id(),
                'role'    => 'owner',
            ]);

            return $chat;
        });
    }

    public function createChannel(string $title, ?string $about = null): Chat
    {
        return $this->createGroup($title, $about)->update(['type' => 'channel']);
    }

    public function addMember(Chat $chat, int $userId, string $role = 'member'): void
    {
        $chat->members()->updateOrCreate(
            ['user_id' => $userId],
            ['role' => $role, 'joined_at' => now()]
        );
        // broadcast
    }

    public function removeMember(Chat $chat, int $userId): void
    {
        $chat->members()->where('user_id', $userId)->delete();
        // broadcast
    }

    public function setRole(Chat $chat, int $userId, string $role): void
    {
        $chat->members()->where('user_id', $userId)->update(['role' => $role]);
    }

    protected function checkAdmin(Chat $chat): void
    {
        $me = $chat->members()->where('user_id', auth()->id())->first();
        abort_if(!$me || !in_array($me->role, ['owner','admin']), 403);
    }

    protected function checkOwner(Chat $chat): void
    {
        abort_if($chat->owner_id !== auth()->id(), 403);
    }

    public function search(string $query, array $relations = [])
    {
        return $this->repository->search($query, $relations);
    }
}
