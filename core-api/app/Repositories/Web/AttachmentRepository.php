<?php

namespace App\Repositories\Web;

use App\Models\Attachment;
use App\Repositories\BaseRepository;

class AttachmentRepository extends BaseRepository
{
    public function __construct(Attachment $model)
    {
        parent::__construct($model, 'attachments');
    }

    public function byUploadKey(string $key): ?Attachment
    {
        return $this->findBy(['upload_key' => $key]);
    }
}
