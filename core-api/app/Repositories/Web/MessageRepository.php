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

        // older (scroll-up)
        if ($beforeId) {
            $query->where('id', '<', $beforeId)
                ->orderByDesc('id');          // newest first, then reverse
        }
        // newer (scroll-down / realtime)
        elseif ($afterId) {
            $query->where('id', '>', $afterId)
                ->orderBy('id');              // chronological
        }
        // first load
        else {
            $query->orderByDesc('id');          // latest N, then reverse
        }

        $messages = $query->limit($limit)->get();

        // ensure chronological order for the frontend
        return $messages->sortBy('id')->values();
    }
}
