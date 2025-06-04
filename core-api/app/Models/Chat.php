<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Chat extends Model
{
    protected $fillable = [
        'type', 'title', 'about',
        'owner_id', 'username'
    ];

    protected $with = ['lastMessage'];

    public function members(): HasMany
    {
        return $this->hasMany(ChatMember::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    public function lastMessage(): HasOne
    {
        return $this->hasOne(Message::class)->latestOfMany();
    }

    public function getResolvedTitleAttribute(): string
    {
        if ($this->type !== 'private') {
            return $this->title ?? 'Unnamed';
        }

        $me = auth()->id();
        $other = $this->members()->where('user_id', '!=', $me)->with('user')->first();
        return $other?->user?->name ?? 'Unknown';
    }

    public function getResolvedUsernameAttribute(): ?string
    {
        if ($this->type !== 'private') {
            return $this->username ? $this->username : null;
        }

        $me = auth()->id();
        $other = $this->members()->where('user_id', '!=', $me)->with('user')->first();
        return $other?->user?->username ?? null;
    }

//    public function getUnreadAttribute(): int
//    {
//        $me = auth()->id();
//
//        return $this->messages()
//            ->whereNull('deleted_at')
//            ->whereDoesntHave('stats', fn ($q) =>
//            $q->where('user_id', $me)->whereNotNull('read_at')
//            )
//            ->count();
//    }
}
