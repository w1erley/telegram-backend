<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MessageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'chat_id'       => $this->chat_id,
            'sender_id'     => $this->sender_id,
            'sender'        => ['id' => $this->sender_id, 'name' => $this->sender?->name],
            'type'          => $this->type,
            'body'          => $this->body,
            'reply_to_id'   => $this->reply_to_id,
            'thread_root_id'=> $this->thread_root_id,
            'created_at'    => $this->created_at?->toIso8601String(),
            'edited_at'     => $this->edited_at?->toIso8601String(),
            'deleted_at'    => $this->deleted_at?->toIso8601String(),
            'stats'         => $this->stats
        ];
    }
}
