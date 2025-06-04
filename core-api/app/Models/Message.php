<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    protected $fillable = [
        'chat_id','sender_id','type','body','caption',
        'reply_to_id','thread_root_id',
        'pinned_at','edited_at','deleted_at'
    ];

    protected $casts = [
        'pinned_at'  => 'datetime',
        'edited_at'  => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $with = ['sender', 'stats'];

    public function chat(): BelongsTo
    {
        return $this->belongsTo(Chat::class);
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class,'sender_id');
    }

    public function stats()
    {
        return $this->hasMany(MessageStat::class);
    }
}
