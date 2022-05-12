<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PatientDocumentResource extends JsonResource
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
            $this->mergeWhen($this->note, fn () => [
                'note' => $this->note,
                'attachments' => $this->attachments,
                'voice_message' => $this->voice_message,
            ]),
            'folder' => $this->whenLoaded('folder', fn () => $this->folder->only(['id', 'title'])),
            'modified_at' => $this->updated_at ?? $this->created_at,
        ];
    }
}
