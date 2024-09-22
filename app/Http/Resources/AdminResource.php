<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdminResource extends JsonResource
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
            'name' => $this->name,
            'nip_nisn' => $this->nip_nisn,
            'email' => $this->email,
            'phone_number' => $this->phone_number,
            'role' => $this->getRoleNames()->first(),
        ];
    }
}
