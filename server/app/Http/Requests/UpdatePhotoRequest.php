<?php

namespace App\Http\Requests;

use App\Models\Photo;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePhotoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Get folder based ID in rute
        $photo = Photo::findOrFail($this->route('photo')->id);
        return Gate::allows('update', $photo);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'folder_id' => 'nullable|exists:folders,id',

        ];
    }

    public function messages(): array
    {
        return [
            'folder_id.required' => 'The folder field is required.',
            'folder_id.exists' => 'The selected folder does not exist.',  
            'folder_id.custom_folder_id_message' => 'The selected folder is invalid.',
        ];
    }
}
