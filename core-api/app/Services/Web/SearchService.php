<?php

namespace App\Services\Web;

use App\Http\Resources\SearchItemResource;
use App\Models\Chat;
use App\Models\Message;
use App\Models\User;
use App\Repositories\Web\ChatRepository;
use App\Repositories\Web\UserRepository;

class SearchService
{
    public function run(string $q, int $me): array
    {
        $myChats = Chat::whereHas('members', fn($x) => $x->where('user_id', $me))
            ->where(function($x) use ($q) {
                $x->where('title', 'ILIKE', "%$q%")
                    ->orWhere('username', 'ILIKE', "%$q%");
            })
            ->get()
            ->mapInto(SearchItemResource::class);

        $global = User::where('name', 'ILIKE', "%$q%")
            ->orWhere('username', 'ILIKE', "%$q%")
            ->limit(30)
            ->get()
            ->mapInto(SearchItemResource::class);

        $msgs = Message::whereHas('chat.members', fn($m) => $m->where('user_id', $me))
            ->where('body', 'ILIKE', "%$q%")
            ->with(['sender'])
            ->limit(50)
            ->get()
            ->mapInto(SearchItemResource::class);

        return [
            'chats'         => $myChats,
            'global_search' => $global,
            'messages'      => $msgs,
        ];
    }
}
