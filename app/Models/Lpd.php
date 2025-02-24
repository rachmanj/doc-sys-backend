<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lpd extends Model
{
    protected $guarded = [];

    public function additionalDocuments()
    {
        return $this->belongsToMany(AdditionalDocument::class);
    }

    public function originDepartment()
    {
        return $this->belongsTo(Department::class, 'origin_department');
    }

    public function destinationDepartment()
    {
        return $this->belongsTo(Department::class, 'destination_department');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function receivedBy()
    {
        return $this->belongsTo(User::class, 'received_by');
    }
}
