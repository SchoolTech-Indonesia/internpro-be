<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PartnerResource extends JsonResource
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
            'address' => $this->address,
            'logo' => $this->logo,
            'file_sk' => $this->file_sk,
            'number_sk' => $this->number_sk,
            'end_date_sk' => $this->end_date_sk,
        ];
    }
}
