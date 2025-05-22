<?php

// app/Http/Resources/SearchItemResource.php
namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\{Chat, User, Message};
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SearchItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $model = $this->resource;

        if ($model instanceof Chat) {
            return [
                'title'        => $this->resolved_title,
                'description'  => $this->about ?? 'Chat',
                'redirect_url' => $this->resolved_username ? $this->resolved_username
                    : "#{$this->id}",
            ];
        }

        if ($model instanceof User) {
            return [
                'title'        => $this->name,
                'description'  => 'User',
                'redirect_url' => '@'.$this->username,
            ];
        }

        return [
            'title'        => $this->sender?->name ?? 'Message',
            'description'  => Str::limit($this->body, 70),
            'message'      => new MessageResource($this),
            'redirect_url' => "#{$this->chat_id}?message_id={$this->id}",
        ];
    }
}
