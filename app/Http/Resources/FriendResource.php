<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FriendResource extends JsonResource
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
            'adding_user' => new UserResource($this->whenLoaded('addingUser')),
            'status' => $this->status,
            'date' => $this->created_at->diffForHumans([
                'parts' => 1,
                'join' => ', ',
                'short' => true,
            ]),
            'added_user' => new UserResource($this->whenLoaded('addedUser')),
        ];
    }
}
