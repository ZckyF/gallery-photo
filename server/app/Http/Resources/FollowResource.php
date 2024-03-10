<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FollowResource extends JsonResource
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
            'follower' => $this->whenLoaded('follower', function() {
                return [
                    'id' => $this->follower->id,
                    'username' => $this->follower->username,
                    'full_name' => $this->follower->full_name,
                    'avatar_name' => $this->follower->avatar_name,
                ];
                
            }),
            'following' => $this->whenLoaded('following', function () {
                return [
                    'id' => $this->following->id,
                    'username' => $this->following->username,
                    'full_name' => $this->following->full_name,
                    'avatar_name' => $this->following->avatar_name,
                ];
            }),
            'created_at' => $this->created_at,
        ];
    }
}
