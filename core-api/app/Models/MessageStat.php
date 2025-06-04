<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MessageStat extends Model
{
    use HasFactory;

    public $timestamps = false;

    public $incrementing = false;
    protected $primaryKey = null;

    protected $with = ['user'];

    protected $fillable = [
        'message_id','user_id','read_at','reaction','reacted_at'
    ];

    protected $casts = [
        'read_at'    => 'datetime',
        'reacted_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
