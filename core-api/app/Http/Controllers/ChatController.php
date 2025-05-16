<?php

namespace App\Http\Controllers;

use App\Http\Resources\ChatResource;
use App\Models\Chat;
use App\Services\Web\ChatService;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function __construct(private ChatService $svc) {}

    public function index()
    {
        return response()->json(
            $this->svc->listForUser(auth()->id())
        );
    }

    public function store(Request $r)
    {
        $data = $r->validate([
            'type'  => 'required|in:group,channel',
            'title' => 'required|string|max:255',
            'about' => 'nullable|string|max:1000'
        ]);

        $chat = $data['type'] === 'group'
            ? $this->svc->createGroup($data['title'], $data['about'])
            : $this->svc->createChannel($data['title'], $data['about']);

        return response()->json($chat);
    }

    public function createPrivate(int $userId)
    {
        return response()->json(
            $this->svc->createPrivate(auth()->id(), $userId)
        );
    }
}
