<?php

namespace App\Traits;

use Illuminate\Support\Facades\Log;

trait Loggable
{
    public function log($message, $data = null): void
    {
        Log::info($message, ['data' => $data]);
    }

    public function error($message): void
    {
        Log::error($message);
    }
}
