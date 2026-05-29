<?php

namespace App\Models;

use Carbon\Carbon;
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
        'pass_number',
        'date',
        'time',
        'gross_weight',
        'tare_weight',
        'net_weight',
        'remarks',
        'payment_value',
        'payment_type',
        'payment_remark',
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
        static::creating(function (Receipt $receipt) {
            if (empty($receipt->pass_number)) {
                $date = $receipt->date ? Carbon::parse($receipt->date) : now();
                $prefix = $date->format('Y/m/');

                $lastReceipt = Receipt::where('pass_number', 'like', $prefix.'%')
                    ->orderBy('pass_number', 'desc')
                    ->first();

                $nextNumber = 1;
                if ($lastReceipt) {
                    $lastNumber = (int) substr($lastReceipt->pass_number, strlen($prefix));
                    $nextNumber = $lastNumber + 1;
                }

                $receipt->pass_number = $prefix.str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
            }
        });

        static::saving(function (Receipt $receipt) {
            $receipt->net_weight = max(0, (float) $receipt->gross_weight - (float) $receipt->tare_weight);
        });
    }
}
