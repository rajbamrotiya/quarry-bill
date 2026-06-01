<?php

namespace App\Models;

use Database\Factories\SupplierFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supplier extends Model
{
    /** @use HasFactory<SupplierFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'mobile_number',
        'email',
        'address',
        'gst_number',
        'pan_number',
        'country',
        'state',
        'district',
        'other_information',
    ];

    public function receipts(): HasMany
    {
        return $this->hasMany(Receipt::class);
    }
}
