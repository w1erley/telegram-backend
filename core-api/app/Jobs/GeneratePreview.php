<?php

namespace App\Jobs;

use App\Models\Attachment;
use Illuminate\Contracts\Queue\ShouldQueue;

class GeneratePreview implements ShouldQueue
{
    public function __construct(public Attachment $att) {}
    public function handle() { /* FFmpeg / Intervention → $att->meta */ }
}
