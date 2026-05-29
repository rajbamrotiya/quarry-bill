<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MaterialType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'hsn_code',
        'unit_rate',
        'other_information',
    ];

    protected $casts = [
        'unit_rate' => 'decimal:2',
    ];

    public function receipts(): HasMany
    {
        return $this->hasMany(Receipt::class);
    }
}
