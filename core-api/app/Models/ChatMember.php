<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatMember extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $table = 'chat_member';

    protected $casts = [
        'permissions' => 'array',
        'joined_at'   => 'datetime',
    ];

    protected $fillable = [
        'chat_id','user_id','role','permissions','is_muted','joined_at'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
