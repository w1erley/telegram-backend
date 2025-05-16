<?php

namespace App\Http\Requests\Attachment;
use Illuminate\Foundation\Http\FormRequest;

class InitRequest extends FormRequest
{
    public function authorize() { return true; }
    public function rules(): array
    {
        return [
            'path' => 'nullable|string|max:255|regex:/^[A-Za-z0-9_\/-]+$/',
            'kind' =>'required|in:image,video,file,audio,voice,gif,circle',
            'size' =>'required|integer|max:'.config('chat.max_size_'.$this->kind),
            'filename' =>'nullable|string'
        ];
    }
}
