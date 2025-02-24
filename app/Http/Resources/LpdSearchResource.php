<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LpdSearchResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'nomor' => $this->nomor,
            'date' => $this->date,
            'origin_department' => $this->originDepartment ? $this->originDepartment->name : null,
            'destination_department' => $this->destinationDepartment ? $this->destinationDepartment->name : null,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'created_by' => $this->createdBy ? $this->createdBy->name : null,
        ];
    }
} 