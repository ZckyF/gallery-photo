<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FolderResource extends JsonResource
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
            'folder_name' => $this->folder_name,
            'description' => $this->description,
            'user' => $this->whenLoaded('user'),
            'photos' => [
                'count' => $this->getTotalPhotosAttribute(), 
                'data' => $this->whenLoaded('photos'), 
            ],
            'created_at' => Carbon::parse($this->created_at)->format('Y-m-d H:i:s')
        ];
    }
}
