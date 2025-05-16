<?php

namespace App\Http\Requests\Message;

use Illuminate\Foundation\Http\FormRequest;

class StoreMessageRequest extends FormRequest
{
    public function authorize() { return true; }

    public function rules(): array
    {
        return [
            'chat_id'     => 'required|exists:chats,id',
            'body'        => 'required|string|max:4096',
            'reply_to_id' => 'nullable|exists:messages,id',
        ];
    }
}
