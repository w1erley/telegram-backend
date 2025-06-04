<?php

namespace Tests\Feature\Tus;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Attachment;
use Illuminate\Support\Facades\Storage;

class TusHookControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_post_finish_marks_attachment_completed_and_moves_file()
    {
        Storage::fake('s3');

        $oldKey = '123abc';
        $originalFilename = 'file.pdf';
        $newKey = 'uploads/temp/' . $originalFilename;

        Storage::disk('s3')->put($oldKey, 'file content');

        $attachment = Attachment::factory()->create([
            'upload_key'   => $oldKey,
            'path_prefix'  => 'uploads/temp',
            'meta'         => ['original_filename' => $originalFilename],
        ]);

        $response = $this->postJson('/api/tus/hooks', [
            'Type' => 'post-finish',
            'Event' => [
                'Upload' => [
                    'ID' => $oldKey,
                    'Size' => 123456,
                ]
            ],
        ]);

        $response->assertStatus(200);
        $response->assertJson(['ok' => true]);

        Storage::disk('s3')->assertMissing($oldKey);
        Storage::disk('s3')->assertExists($newKey);

        $this->assertDatabaseHas('attachments', [
            'id' => $attachment->id,
            'status' => 'completed',
            'size' => 123456,
            'path' => $newKey,
        ]);
    }
}
