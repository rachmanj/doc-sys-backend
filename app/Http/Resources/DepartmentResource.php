<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DepartmentResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'project' => $this->project,
            'location_code' => $this->location_code,
            'transit_code' => $this->transit_code,
            'akronim' => $this->akronim,
            'sap_code' => $this->sap_code,
        ];
    }
} 