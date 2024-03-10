<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'username' => $this->username,
            'email' => $this->email,
            'full_name' => $this->full_name,
            'address' => $this->address,
            'is_actived' => $this->is_actived,
            'bio' => $this->bio,
            'latest_photo' => $this->latestPhoto ? new PhotoResource($this->latestPhoto) : null,
            'avatar_name' => $this->avatar_name,
            // 'permissions' => $this->getAllPermissions()->pluck('name'),
            'role' => $this->getRoleNames(),
        ];
    }
}
