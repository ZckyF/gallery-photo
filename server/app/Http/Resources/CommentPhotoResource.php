<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentPhotoResource extends JsonResource
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
            'comment' => $this->comment,
            'photo' => $this->whenLoaded('photo'),
            'user' => [
                'id' => $this->user->id,
                'username' => $this->user->username,
                'avatar_name' => $this->user->avatar_name,
                'created_at' => Carbon::parse($this->created_at)->format('Y-m-d H:i:s'),
            ],
            'parent_id' => $this->parent_id,
            'replies' => $this->when($this->parent_id === null, CommentPhotoResource::collection($this->replies)),
            'created_at' => $this->created_at,
        ];
    }
}
