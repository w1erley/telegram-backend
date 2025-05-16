<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatMember extends Model
{
    public $timestamps = false;

    protected $table = 'chat_member';

    protected $casts = [
        'permissions' => 'array',
        'joined_at'   => 'datetime',
    ];

    protected $fillable = [
        'chat_id','user_id','role','permissions','is_muted','joined_at'
    ];
}
