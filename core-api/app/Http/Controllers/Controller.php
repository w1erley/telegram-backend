<?php

namespace App\Http\Controllers;
use Illuminate\Http\JsonResponse;

abstract class Controller
{
    protected function ok(mixed $data = null, $code): JsonResponse
    {
        return response()->json($data, $code);
    }

    protected function fail(string $msg = '_FAILED_', int $code = 400): JsonResponse
    {
        return response()->json(['message' => $msg], $code);
    }
}
