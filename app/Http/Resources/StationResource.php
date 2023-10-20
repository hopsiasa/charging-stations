<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'address' => $this->address,
            'created_at' => (string) $this->created_at,
            'updated_at' => (string) $this->updated_at,
//            'company' => $this->company,
        ];
    }
}
