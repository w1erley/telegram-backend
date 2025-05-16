<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class LoginRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'email' => 'nullable|string|email|max:255',
            'name' => 'nullable|string|max:255',
            'password' => 'required|string|min:8',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $hasEmail = $this->filled('email');
            $hasName = $this->filled('name');

            if (!$hasEmail && !$hasName) {
                $validator->errors()->add('email', 'Either email or name is required.');
                $validator->errors()->add('name', 'Either name or email is required.');
            }
        });
    }
}
