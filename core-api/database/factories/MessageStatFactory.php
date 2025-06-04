<?php

namespace Database\Factories;

use App\Models\MessageStat;
use Illuminate\Database\Eloquent\Factories\Factory;

class MessageStatFactory extends Factory
{
    protected $model = MessageStat::class;

    public function definition(): array
    {
        return [
            'message_id' => \App\Models\Message::factory(),
            'user_id' => \App\Models\User::factory(),
            'read_at' => now(),
            'reaction' => '',
            'reacted_at' => null,
        ];
    }
}
