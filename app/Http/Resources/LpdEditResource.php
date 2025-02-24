<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LpdEditResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'nomor' => $this->nomor,
            'date' => $this->date,
            'origin_department' => $this->originDepartment,
            'destination_department' => $this->destinationDepartment,
            'attention_person' => $this->attention_person,
            'created_by' => $this->createdBy,
            'sent_at' => $this->sent_at,
            'received_at' => $this->received_at,
            'received_by' => $this->receivedBy,
            'notes' => $this->notes,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
} 