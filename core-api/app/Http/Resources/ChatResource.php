<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChatResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'type'        => $this->type,
            'title'       => $this->resolved_title,
            'username'    => $this->resolved_username,
//            'alias'       => $this->alias,
            'unread'      => $this->unread ?? 0,
            'last_message'=> $this->last_message?->body,
            'last_message_time' => optional($this->last_message?->created_at)->toIso8601String(),
            'me_id'       => $request->user()->id,
            'alias' => $this->resolved_username ? $this->resolved_username
                : "#{$this->id}",
        ];
    }
}
