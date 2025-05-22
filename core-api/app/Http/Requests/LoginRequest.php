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
            'username' => 'nullable|string|max:255',
            'password' => 'required|string|min:8',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $hasEmail = $this->filled('email');
            $hasName = $this->filled('username');

            if (!$hasEmail && !$hasName) {
                $validator->errors()->add('email', 'Either email or name is required.');
                $validator->errors()->add('username', 'Either username or email is required.');
            }
        });
    }
}
