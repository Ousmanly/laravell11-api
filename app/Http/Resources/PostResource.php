<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

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
            "id"=> $this->id,
            "post_title"=> $this->title,
            "post_content"=> $this->content,
            "Created_by"=> $this->user->name,
            "published_at"=> $this->created_at->format('d-m-y'),
            "last_update"=> Carbon::parse($this->updated_at)->diffForHumans()
        ];
    }
}
