<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $lastOnline = Carbon::parse($this->lastOnline);

        // Ensure lastOnline is treated as a past date
        if ($lastOnline->isFuture()) {
            $lastOnline = Carbon::now();
        }

        return [
            'id' => $this->id,
            'name' => $this->name,
            'profile' => $this->profile,
            'bio' => $this->bio ? $this->bio : null,
            'lastOnline' => $lastOnline->diffForHumans([
                'parts' => 1,
                'join' => ', ',
                'short' => true,
                'syntax' => Carbon::DIFF_RELATIVE_TO_NOW,
            ]),
        ];
    }
}
