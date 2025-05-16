<?php

namespace App\Http\Requests\Message;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMessageRequest extends FormRequest
{
    public function authorize() { return true; }

    public function rules(): array
    {
        return ['body' => 'required|string|max:4096'];
    }
}
