<?php

namespace App\Services\Web;

use Illuminate\Support\Facades\Http;

use App\Repositories\Web\AttachmentRepository;
use App\Services\BaseService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class AttachmentService extends BaseService
{
    public function __construct(AttachmentRepository $repo)
    {
        parent::__construct($repo);
    }

    public function init(array $data): array
    {
        $size = (int) $data['size'];
        $kind = $data['kind'] ?? 'file';
        $pathPrefix = trim($data['path'] ?? "", '/');
        $filename = $data['filename'] ?? 'untitled';
        $userId = $data['user_id'];

        $metadata = [
            'filename' => base64_encode($filename),
            'kind'     => base64_encode($kind),
        ];

        $uploadMetadata = collect($metadata)
            ->map(fn($v, $k) => "{$k} {$v}")
            ->implode(',');

        $response = Http::withHeaders([
            'Tus-Resumable' => '1.0.0',
            'Upload-Length' => $size,
            'Upload-Metadata' => $uploadMetadata,
        ])->post(config('filesystems.disks.tusd.endpoint'));

        if (!$response->created()) {
            throw new \RuntimeException('Failed to initialize tus upload: ' . $response->body());
        }

        $location = $response->header('Location');
        $uploadKey = basename($location);

        Log::info(["data" => $data]);

        $attachment = $this->repository->store([
            'upload_key' => $uploadKey,
            'kind' => $kind,
            'size' => $size,
            'status' => 'init',
            'user_id' => $userId,
            'path_prefix'  => $pathPrefix,
            'meta' => ['original_filename' => $filename],
        ]);

        $uploadUrl = rtrim(config('filesystems.disks.tusd.endpoint'), '/') . "/files/{$uploadKey}";

        return [$attachment, $uploadUrl];
    }

    public function markCreated(string $uploadKey): void
    {
        $attachment = $this->repository->findBy(['upload_key' => $uploadKey]);
        if ($attachment) {
            $this->repository->update($attachment->id, ['status' => 'created']);
        }
    }

    public function markCompleted(string $key, ?int $size = null): void
    {
        $attachment = $this->repository->findBy(['upload_key' => $key]);
        if (!$attachment) {
            throw new \RuntimeException('Attachment not found');
        }

        $originalFilename = $attachment->meta['original_filename'] ?? null;
        $prefix = trim($attachment->path_prefix);

        $oldKey = explode('+', $key)[0];
        $newKey = $prefix ? "{$prefix}/{$originalFilename}" : $originalFilename;

        $disk = Storage::disk('s3');

        if ($disk->exists($oldKey) && !$disk->exists($newKey)) {
            $disk->copy($oldKey, $newKey);
            $disk->delete($oldKey);
        } else {
            $newKey = $disk->exists($newKey) ? $newKey : $oldKey;
            logger()->error("File not found in storage: $oldKey");
        }

        $this->repository->update($attachment->id, [
            'size'   => $size,
            'status' => 'completed',
            'path'   => $newKey,
        ]);
    }
}
