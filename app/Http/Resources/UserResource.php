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
            'phone_number' => $this->phone_number,
            'roles' => $this->roles->pluck('name')->first(),
            'major' => [
                'uuid' => $this->major->uuid,
                // 'major_code' => $this->major->major_code,
                'major_name' => $this->major->major_name,
            ],
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}