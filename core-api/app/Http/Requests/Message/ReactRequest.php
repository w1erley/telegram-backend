<?php

namespace App\Http\Requests\Message;

use Illuminate\Foundation\Http\FormRequest;

class ReactRequest extends FormRequest
{
    public function authorize() { return true; }

    public function rules(): array
    {
        return ['emoji' => 'required|string|max:16'];
    }
}
