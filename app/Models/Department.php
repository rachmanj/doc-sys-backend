<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $fillable = [
        'department_name',
        'project',
        'location_code',
        'transit_code',
        'akronim',
        'sap_code'
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
