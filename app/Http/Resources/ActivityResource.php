<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ActivityResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'program_id' => $this->program_id,
            'name' => $this->name,
            'partner_id' => $this->partner_id,
            'teacher_id' => $this->teacher_id,
            'description' => $this->description,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
        ];
    }
}
