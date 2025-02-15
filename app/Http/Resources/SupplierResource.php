<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SupplierResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        return [
            "id" => $this->id,
            "sap_code" => $this->sap_code,
            "name" => $this->name,
            "type" => $this->type,
            "city" => $this->city,
            "payment_project" => $this->payment_project,
            "is_active" => $this->is_active,
        ];
    }
}
