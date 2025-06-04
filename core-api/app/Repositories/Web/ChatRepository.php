<?php

namespace App\Repositories\Web;

use App\Models\Chat;
use App\Repositories\BaseRepository;

class ChatRepository extends BaseRepository
{
    public function __construct(Chat $model)
    {
        parent::__construct($model, 'chat');
    }

    public function listForUserWithMeta(int $userId)
    {
        return $this->model
            ->whereHas('members', fn ($q) => $q->where('user_id', $userId))
            ->with(['lastMessage' => fn ($q) => $q->latest()->with('sender')])
            ->withCount([
                'messages as unread' => fn ($q) =>
                $q->whereNull('deleted_at')
                    ->whereDoesntHave('stats', fn ($s) =>
                    $s->where('user_id', $userId)->whereNotNull('read_at')
                    ),
            ])
            ->get();
    }

    public function findPrivateBetween(int $userA, int $userB): ?Chat
    {
        return $this->model
            ->where('type', 'private')
            ->whereHas('members', function ($q) use ($userA, $userB) {
                $q->whereIn('user_id', [$userA, $userB]);
            }, '=', 2)
            ->first();
    }


    public function search(string $query, array $relations = [])
    {
        return $this->model
            ->where('name', 'ILIKE', "%{$query}%")
            ->with($relations)
            ->get();
    }
}
