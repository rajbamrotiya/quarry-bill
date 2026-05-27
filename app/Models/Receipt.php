<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Receipt extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'vehicle_number',
        'material_type_id',
        'royalty_number',
        'date',
        'time',
        'gross_weight',
        'tare_weight',
        'net_weight',
        'remarks',
        'payment_value',
        'payment_type',
    ];

    protected $casts = [
        'date' => 'date',
        'gross_weight' => 'decimal:3',
        'tare_weight' => 'decimal:3',
        'net_weight' => 'decimal:3',
        'payment_value' => 'decimal:2',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function materialType(): BelongsTo
    {
        return $this->belongsTo(MaterialType::class);
    }

    protected static function booted(): void
    {
        static::saving(function (Receipt $receipt) {
            $receipt->net_weight = max(0, (float) $receipt->gross_weight - (float) $receipt->tare_weight);
        });
    }
}
