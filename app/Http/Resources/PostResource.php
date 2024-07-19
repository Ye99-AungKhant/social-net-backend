<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
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
            'content' => $this->content,
            'status' => $this->status,
            'date' => $this->created_at->diffForHumans([
                'parts' => 1,
                'join' => ', ',
                'short' => true,
            ]),
            'like_count' => $this->like_count,
            'comment_count' => $this->comment_count,
            'liked' => LikeResource::collection($this->like),
            'image' => MediaResource::collection($this->media),
            'user' => new UserResource($this->user),

        ];
    }
}
