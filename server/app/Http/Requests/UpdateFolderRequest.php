<?php

namespace App\Http\Requests;

use App\Models\Folder;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;

class UpdateFolderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
       
        // Get folder based ID in rute
        $folder = Folder::findOrFail($this->route('folder')->id);
        return Gate::allows('update', $folder);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        
        $userId = auth()->id();
        return [
            'folder_name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('folders')->where(function ($query) use ($userId) {
                    return $query->where('user_id', $userId);
                }),
            ],
            'description' => 'nullable|string|max:255',
        ];
    }
}
