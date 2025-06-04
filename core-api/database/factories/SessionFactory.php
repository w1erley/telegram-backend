<?php

namespace Database\Factories;

use App\Models\Session;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class SessionFactory extends Factory
{
    protected $model = Session::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'personal_access_token_id' => null,
            'device' => $this->faker->userAgent,
            'ip_address' => $this->faker->ipv4,
            'browser' => $this->faker->userAgent,
            'last_active_at' => now(),
        ];
    }
}
