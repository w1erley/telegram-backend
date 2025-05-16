<?php

namespace App\Repositories\Web;

use App\Models\Message;
use App\Repositories\BaseRepository;

class MessageRepository extends BaseRepository
{
    public function __construct(Message $model)
    {
        parent::__construct($model, 'message');
    }

    public function fetchHistory(int $chatId, ?int $afterId = null, int $limit = 50)
    {
        return $this->model
            ->where('chat_id', $chatId)
            ->when($afterId, fn($q) => $q->where('id', '>', $afterId))
            ->orderBy('id')
            ->take($limit)
            ->get();
    }
}
