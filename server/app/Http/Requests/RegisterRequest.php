<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules()
    {
        return [
            'username' => 'required|min:4|max:30|unique:users',
            'full_name' => 'required|max:100',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8',
            'address' => 'required|min:5',
            'otp' => 'required|string|min:6|max:6',
            
        ];
    }

    public function messages()
    {
        return [
            'username.unique' => 'The provided username is already registered.',
            'email.unique' => 'The provided email is already registered.',
        ];
    }
}
