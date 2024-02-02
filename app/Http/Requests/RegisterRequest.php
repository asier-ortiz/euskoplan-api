<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'username' => 'required|unique:users|string|min:3|max:20',
            'email' => 'required|unique:users|email',
            'password' => 'required|string|min:6|max:20',
            'password_confirm' => 'required|same:password'
        ];
    }
}
