<?php

namespace App\Models;

use App\Events\Auth\Sessions\SessionTerminated;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Event;
use Laravel\Sanctum\PersonalAccessToken;

class Session extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'personal_access_token_id',
        'device',
        'ip_address',
        'browser',
        'last_active_at',
    ];

    protected $dates = [
        'last_active_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function token()
    {
        return $this->belongsTo(PersonalAccessToken::class, 'personal_access_token_id');
    }

    protected static function booted()
    {
        static::deleting(function ($session) {
            if ($session->token) {
                $session->token->delete();
            }
            Event::dispatch(new SessionTerminated($session));
        });
    }
}
