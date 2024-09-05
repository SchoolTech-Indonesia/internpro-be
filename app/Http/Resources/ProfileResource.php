<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'name' => $this->name,
            'nip_nisn' => $this->nip_nisn,
            'email' => $this->email,
            'phone_number' => $this->phone_number,
            'school' => new SchoolResource($this->whenLoaded('school')),
            'major' => new MajorResource($this->whenLoaded('major')),
        ];
    }
}
