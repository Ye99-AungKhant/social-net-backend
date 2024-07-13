<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
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
            'post_id' => $this->post_id,
            'user_id' => $this->user_id,
            'content' => $this->content,
            'date' => $this->created_at->diffForHumans([
                'parts' => 1,
                'join' => ', ',
                'short' => true,
            ]),
            'user' => new UserResource($this->user)
        ];
    }
}
