<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class Save2Resource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user' => $this->whenLoaded('user'),
            'photo' =>[
                'id' => $this->photo->id,
                'title' => $this->photo->title,
                'file_name' => $this->photo->file_name,
                'description' => $this->photo->description,
                'user' => [
                    'id' => $this->photo->user->id,
                    'username' => $this->photo->user->username,
                    'avatar_name' => $this->photo->user->avatar_name,
                ]
                
                
            ]
            
        ];
    }
}
