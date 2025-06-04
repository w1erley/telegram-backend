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

    public function fetchHistory(
        int  $chatId,
        ?int $beforeId = null,
        ?int $afterId  = null,
        int  $limit    = 20
    ) {
        $query = $this->model
            ->where('chat_id', $chatId);

        if ($beforeId) {
            $query->where('id', '<', $beforeId)
                ->orderByDesc('id');
        }
        elseif ($afterId) {
            $query->where('id', '>', $afterId)
                ->orderBy('id');
        }
        else {
            $query->orderByDesc('id');
        }

        $messages = $query->limit($limit)->get();

        return $messages->sortBy('id')->values();
    }
}
