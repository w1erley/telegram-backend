<?php

namespace Database\Factories;

use App\Models\ChatMember;
use Illuminate\Database\Eloquent\Factories\Factory;

class ChatMemberFactory extends Factory
{
    protected $model = ChatMember::class;

    public function definition(): array
    {
        return [
            'chat_id' => \App\Models\Chat::factory(),
            'user_id' => \App\Models\User::factory(),
            'role' => $this->faker->randomElement(['owner', 'admin', 'member']),
            'permissions' => [],
            'is_muted' => false,
            'joined_at' => now(),
        ];
    }
}
