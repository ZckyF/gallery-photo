<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PhotoResource extends JsonResource
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
            'title' => $this->title,
            'description' => $this->description,
            'file_name' => $this->file_name,
            'user' => $this->whenLoaded('user'),
            'folder' => $this->whenLoaded('folder'),
            'comments' => CommentPhotoResource::collection($this->whenLoaded('comments')), // Menggunakan CommentResource
            'likes' => $this->whenLoaded('likes'),
            'saves' => $this->whenLoaded('saves'),
            'created_at' => Carbon::parse($this->created_at)->format('Y-m-d H:i:s'),
        ];
    }
    
}
