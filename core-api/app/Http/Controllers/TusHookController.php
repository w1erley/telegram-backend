<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Services\Web\AttachmentService;
use Illuminate\Support\Facades\Log;

class TusHookController extends Controller
{
    public function __invoke(Request $request, AttachmentService $service)
    {
        $type   = $request->input('Type');
        $upload = $request->input('Event.Upload', []);
        $key    = $upload['ID']   ?? null;
        $size   = $upload['Size'] ?? null;

        Log::info("Upload id: {$key}");

        if ($type === 'post-finish') {
            if (!$key) {
                return response()->json(['message' => 'Missing upload ID'], 400);
            }

            $service->markCompleted($key, $size);
        }

        return response()->json(['ok' => true]);
    }
}
