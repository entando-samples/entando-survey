<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DocumentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'body' => $this->body,
            'attachment' => $this->file,
            'video' => $this->youtube_url,
            'cover_image' => $this->cover_image,
            'images' => $this->images ?? [],
            'is_read' => $this->pivot['is_read'] ? true : false,
            'sent_at' => $this->pivot['created_at'],
        ];
    }
}
