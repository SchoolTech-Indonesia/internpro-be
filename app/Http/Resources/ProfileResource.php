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
        $role = $this->getRoleNames()->first();
        return [
            'name' => $this->name,
            'nip_nisn' => $this->nip_nisn,
            'email' => $this->email,
            'phone_number' => $this->phone_number,
            'role' => $role,
            'school' => $this->whenLoaded('school', function () {
                return [
                    'uuid' => $this->school->uuid,
                    'name' => $this->school->school_name,
                ];
            }),
            'major' => $this->whenLoaded('major', function () {
                return [
                    'uuid' => $this->major->uuid,
                    'name' => $this->major->major_name,
                ];
            }),
            'class' => $this->whenLoaded('class', function () {
                return [
                    'uuid' => $this->class->uuid,
                    'name' => $this->class->class_name,
                ];
            }),
            'partner' => $this->whenLoaded('partners', function () {
                return $this->partners->map(function ($partner) {
                    return [
                        'uuid' => $partner->uuid,
                        'name' => $partner->name,
                    ];
                });
            }),
           
        ];
    }
}