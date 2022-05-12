<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MessageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $is_sent = $this->sender_id == auth()->id() ? true : false;

        $is_read = $is_sent
            ? ($this->reply ? ($this->reply->read_at ? true : false) : true)
            : ($this->read_at ? true : false);

        return [
            "id" => $this->id,
            "title" => optional($this->topic)->title ?? '',
            "body" => $this->body,
            "attachments" => $this->attachments ?? [],
            "voice_message" => $this->voice_message,
            "is_sent" => $is_sent,
            "is_read" =>  $is_read,
            "created_at" => $this->created_at,
        ];
    }
}
