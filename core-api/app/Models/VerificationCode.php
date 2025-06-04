<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Carbon\Carbon;

class VerificationCode extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'code', 'expires_at'];

    public static function generateForUser($user)
    {
        return self::create([
            'user_id' => $user->id,
            'code' => Str::random(40),
            'expires_at' => Carbon::now()->addMinutes(10),
        ]);
    }

    public function isExpired()
    {
        return Carbon::now()->greaterThan($this->expires_at);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
