<?php

namespace Tests\Feature\Attachment;

use Tests\TestCase;
use App\Models\User;
use App\Models\Session;
use Illuminate\Support\Facades\Http;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AttachmentControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_init_attachment_returns_attachment_and_upload_url()
    {
        $user = User::factory()->create();
        $token = $this->authenticate($user);

        Http::fake([
            '*' => Http::response(null, 201, ['Location' => 'http://tusd.test/files/abc123']),
        ]);

        $payload = [
            'filename' => 'test_file.pdf',
            'size'     => 123456,
            'kind'     => 'file',
            'path'     => 'user_uploads/temp',
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/attachments/init', $payload);

        $response->assertOk()
            ->assertJsonStructure([
                'attachment' => ['id', 'upload_key', 'kind', 'status', 'size', 'user_id'],
                'uploadUrl',
            ]);
    }
}
