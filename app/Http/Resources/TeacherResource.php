<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TeacherResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'uuid' => $this->uuid,
            'nip_nisn' => $this->nip_nisn,
            'name' => $this->name,
            'email' => $this->email,
            'phone_number' => $this->phone_number,
            'school' => new SchoolResource($this->whenLoaded('school')),
        ];
    }
}
