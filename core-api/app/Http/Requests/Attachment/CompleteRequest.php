<?php

namespace App\Http\Requests\Attachment;
use Illuminate\Foundation\Http\FormRequest;

class CompleteRequest extends FormRequest
{
    public function authorize() { return true; }
    public function rules(): array
    {
        return [
            'uploadKey'=>'required|uuid|exists:attachments,upload_key',
            'mime'=>'required|string|max:255',
        ];
    }
}
