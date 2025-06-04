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
            'kind' => 'required|in:image,video,file,audio,voice,gif,circle',
            'size' => ['required', 'integer'],
            'filename' => 'nullable|string',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $kind = $this->input('kind');
            $max = config('chat.max_size_' . $kind);

            if ($max !== null && $this->input('size') > $max) {
                $validator->errors()->add('size', 'The size may not be greater than ' . $max . '.');
            }
        });
    }

}
