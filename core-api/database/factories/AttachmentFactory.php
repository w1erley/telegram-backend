<?php

namespace Database\Factories;

use App\Models\Attachment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AttachmentFactory extends Factory
{
    protected $model = Attachment::class;

    public function definition(): array
    {
        return [
            'upload_key' => $this->faker->uuid(),
            'kind'       => 'file',
            'status'     => 'init',
            'size'       => 123456,
            'mime'       => 'application/pdf',
            'meta'       => ['original_filename' => 'test.pdf'],
            'path'       => null,
            'path_prefix' => 'uploads/temp',
            'user_id'    => User::factory(),
        ];
    }
}
