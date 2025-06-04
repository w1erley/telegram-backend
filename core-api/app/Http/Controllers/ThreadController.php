<?php

namespace App\Http\Controllers;

use App\Http\Requests\Message\StoreMessageRequest;
use App\Models\Message;
use App\Services\Web\ThreadService;

class ThreadController extends Controller
{
    public function index(Message $root, ThreadService $svc)
    {
        return response()->json(
            $svc->history($root, request('after'))
        );
    }

    public function count(Message $root, ThreadService $svc)
    {
        return response()->json(['count' => $svc->count($root)]);
    }

    public function store(Message $root, StoreMessageRequest $r, ThreadService $svc)
    {
        return response()->json(
            $svc->sendComment($root, $r->body)
        );
    }
}
