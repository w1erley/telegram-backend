<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('private-chat.{chatId}', function ($user, $chatId) {
    return $user->chats()->where('chat_id', $chatId)->exists();
});
