<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SchoolResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            /**
             * uuid is a unique identifier for the school
             */
            'uuid' => $this->uuid,
            'school_name' => $this->school_name,
            'school_address' => $this->school_address,
            'phone_number' => $this->phone_number,
            'start_member' => $this->start_member,
            'end_member' => $this->end_member,
        ];
    }
}
