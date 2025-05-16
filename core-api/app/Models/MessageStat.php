<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MessageStat extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'message_id','user_id','read_at','reaction','reacted_at'
    ];

    protected $casts = [
        'read_at'    => 'datetime',
        'reacted_at' => 'datetime',
    ];
}
