<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Get folder based ID in rute
        $user = User::findOrFail($this->route('user')->id);
        return Gate::allows('update', $user);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'username' => 'required|string|min:4|max:30|'.Rule::unique('users', 'username')->ignore($this->route('user')),
            'full_name' => 'required|string|max:50',
            'address' => 'required|string|max:255',
            'email' => 'required|email|max:255|'.Rule::unique('users', 'email')->ignore($this->route('user')),
            'bio' => 'nullable|string|max:255',
            'file' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];
    }

    
}
