<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'name' => ucfirst($this->name),
            'email' => $this->email,
            'id' => $this->id,
            'is_alertable' => $this->is_alertable,
            'role' => ucfirst($this->role),
            'updated_at' => [
                'human' => $this->updated_at->diffForHumans(),
                'date' => $this->updated_at
            ],
            'created_at' => [
                'human' => $this->created_at->diffForHumans(),
                'date' => $this->created_at
            ],
            'email_verified' => $this->email_verified_at ? true : false,
        ];
    }
}
