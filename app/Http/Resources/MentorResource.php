<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MentorResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $Partner = $this->partners->first();
        return [
            'id' => $this->uuid,
            'nip_nisn' => $this->nip_nisn,
            'name' => $this->name,
            'email' => $this->email,
            'phone_number' => $this->phone_number,
            'school' => new SchoolResource($this->school),
            'partner' =>[
                'uuid' => $Partner->uuid,
                'name' => $Partner->name,
            ] ,
            'roles' => $this->roles->pluck('name')->first(),
        ];
    }
}