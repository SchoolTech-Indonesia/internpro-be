<?php

namespace App\Http\Resources;

use App\Services\S3Service;
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
            'logo' => S3Service::getUrl($this->logo),
            'file_sk' => S3Service::getUrl($this->file_sk),
            'number_sk' => $this->number_sk,
            // type : DateTime
            'end_date_sk' => $this->end_date_sk,
            'mentors'=> UserResource::collection($this->whenLoaded('users'))
        ];
    }
}
