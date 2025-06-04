<?php

namespace Database\Factories;

use App\Models\Message;
use Illuminate\Database\Eloquent\Factories\Factory;

class MessageFactory extends Factory
{
    protected $model = Message::class;

    public function definition(): array
    {
        return [
            'chat_id' => \App\Models\Chat::factory(),
            'sender_id' => \App\Models\User::factory(),
            'type' => 'plain',
            'body' => $this->faker->sentence,
            'caption' => null,
            'reply_to_id' => null,
            'thread_root_id' => null,
            'pinned_at' => null,
            'edited_at' => null,
            'deleted_at' => null,
        ];
    }
}
