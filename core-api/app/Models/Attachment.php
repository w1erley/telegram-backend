<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Attachment extends Model
{
    protected $fillable = [
        'upload_key', 'kind', 'status',
        'size', 'mime', 'meta',
        'path', 'path_prefix', 'user_id',
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $model) {
            if (empty($model->upload_key)) {
                $model->upload_key = Str::uuid();
            }
        });
    }

    public function getUrlAttribute(): ?string
    {
        return $this->path
            ? config('filesystems.disks.s3.url').'/'.$this->path
            : null;
    }
}
