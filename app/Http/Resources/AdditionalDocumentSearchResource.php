<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdditionalDocumentSearchResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'document_number' => $this->document_number,
            'document_date' => $this->document_date,
            'receive_date' => $this->receive_date,
            'po_no' => $this->po_no,
            'project' => $this->project,
            'status' => $this->status,
            'cur_loc' => $this->cur_loc,
            'type' => [
                'id' => $this->type?->id,
                'name' => $this->type?->type_name,
            ],
            'created_by' => [
                'id' => $this->createdBy?->id,
                'name' => $this->createdBy?->name,
            ],
            'invoice' => [
                'id' => $this->invoice?->id,
                'invoice_number' => $this->invoice?->invoice_number,
            ],
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
