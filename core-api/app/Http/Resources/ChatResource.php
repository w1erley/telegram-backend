<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class ChatResource extends JsonResource
{
    protected int $userId;

    public function __construct($resource, int $userId = null)
    {
        parent::__construct($resource);
        $this->userId = $userId ?? auth()->id();
    }

    public static function collection($resource, int $userId = null): Collection
    {
        return collect($resource)->map(fn($item) => new static($item, $userId));
    }

    public function toArray(Request $request): array
    {
        $isPrivate = $this->type === 'private';

        if ($isPrivate) {
            $other = $this->members
                ->first(fn($m) => $m->user_id !== $this->userId);

            $title     = $other?->user?->name     ?? 'Unknown';
            $username  = $other?->user?->username
                ? "@{$other->user->username}"
                : null;
            $alias     = $username ?: "-{$this->id}";
            $recipient = $other?->user_id;
        } else {
            $title     = $this->title       ?? 'Unnamed';
            $username  = $this->username    ?: null;
            $alias     = $this->alias       ?? null;
            $recipient = null;
        }

        $unreadCount = $this->messages()
            ->whereNull('deleted_at')
            ->whereDoesntHave('stats', fn ($q) =>
            $q->where('user_id', $this->userId)
                ->whereNotNull('read_at')
            )
            ->count();

        return [
            'id'                => $this->id,
            'created_at'        => $this->created_at,
            'type'              => $this->type,
            'title'             => $title,
            'username'          => $username,
            'alias'             => $alias,
            'unread'            => $unreadCount ?? 0,
            'last_message'      => $this->lastMessage,
            'me_id'             => $this->userId,
            'recipient_id'      => $recipient,
        ];
    }
}
