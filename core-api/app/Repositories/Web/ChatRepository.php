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
            ->whereHas('members', fn($q) => $q->where('user_id', $userId))
            ->with(['lastMessage' => fn($q) => $q->latest()->limit(1)])
            ->withCount([
                'messages as unread' => fn($q) =>
                $q->whereNull('deleted_at')
                    ->whereDoesntHave('stats', fn($s) =>
                    $s->where('user_id', $userId)->whereNotNull('read_at')
                    ),
            ])
            ->get();
    }

    public function findPrivateBetween(int $userA, int $userB): ?Chat
    {
        return $this->cacheQuery(
            "private_{$userA}_{$userB}",
            fn () => $this->model
                ->where('type', 'private')
                ->whereHas('members', fn($q) => $q->whereIn('user_id', [$userA, $userB]))
                ->first()
        );
    }

    public function search(string $query, array $relations = [])
    {
        return $this->model
            ->where('name', 'ILIKE', "%{$query}%")
            ->with($relations)
            ->get();
    }
}
