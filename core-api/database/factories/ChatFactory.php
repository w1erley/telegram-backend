<?php

namespace Database\Factories;

use App\Models\Chat;
use Illuminate\Database\Eloquent\Factories\Factory;

class ChatFactory extends Factory
{
    protected $model = Chat::class;

    public function definition(): array
    {
        return [
            'type' => $this->faker->randomElement(['private', 'group', 'channel']),
            'title' => $this->faker->words(3, true),
            'about' => $this->faker->sentence,
            'owner_id' => \App\Models\User::factory(),
            'username' => $this->faker->unique()->userName,
        ];
    }

    public function private(): Factory
    {
        return $this->state(fn(array $attributes) => [
            'type' => 'private',
        ]);
    }
}
