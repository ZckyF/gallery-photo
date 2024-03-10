<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'username' => 'required|string|max:255',
            'password' => 'required|string|min:8',
            'email' => 'required|email|unique:users',
            'full_name' => 'required|string|max:255',
            'address' => 'required|string',
            'is_actived' => 'required|boolean',
            'avatar_name' => 'required|string|max:255'
        ];
    }
}
