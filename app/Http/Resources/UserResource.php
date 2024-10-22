<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'nip/nisn' => $this->nip_nisn,
            'name' => $this->name,
            'email' => $this->email,
            'roles' => $this->roles->select(['id', 'name'])->first(),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}