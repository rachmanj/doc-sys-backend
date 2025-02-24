<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Spi extends Model
{
    protected $guarded = [];

    public function invoices(): BelongsToMany
    {
        return $this->belongsToMany(Invoice::class, 'invoice_spi');
    }
}
