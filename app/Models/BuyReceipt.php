<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BuyReceipt extends Model
{
    use HasFactory;

    protected $fillable = [
        'supplier_id',
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
    ];

    protected $casts = [
        'date' => 'date',
        'gross_weight' => 'integer',
        'tare_weight' => 'integer',
        'net_weight' => 'integer',
    ];

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function materialType(): BelongsTo
    {
        return $this->belongsTo(MaterialType::class);
    }

    public function histories(): HasMany
    {
        return $this->hasMany(BuyReceiptHistory::class)->latest();
    }

    protected static function booted(): void
    {
        static::created(function (BuyReceipt $buy_receipt) {
            $buy_receipt->histories()->create([
                'user_id' => auth()->id(),
                'event' => 'created',
                'changes' => $buy_receipt->getAttributes(),
            ]);
        });

        static::updated(function (BuyReceipt $buy_receipt) {
            $changes = [];
            foreach ($buy_receipt->getChanges() as $key => $value) {
                if ($key === 'updated_at') {
                    continue;
                }
                $changes[$key] = [
                    'old' => $buy_receipt->getOriginal($key),
                    'new' => $value,
                ];
            }

            if (! empty($changes)) {
                $buy_receipt->histories()->create([
                    'user_id' => auth()->id(),
                    'event' => 'updated',
                    'changes' => $changes,
                ]);
            }
        });

        static::creating(function (BuyReceipt $buy_receipt) {
            if (empty($buy_receipt->pass_number)) {
                $date = $buy_receipt->date ? Carbon::parse($buy_receipt->date) : now();
                $prefix = 'BUY/' . $date->format('Y/m/');

                $lastBuyReceipt = BuyReceipt::where('pass_number', 'like', $prefix.'%')
                    ->orderBy('pass_number', 'desc')
                    ->first();

                $nextNumber = 1;
                if ($lastBuyReceipt) {
                    $lastNumber = (int) substr($lastBuyReceipt->pass_number, strlen($prefix));
                    $nextNumber = $lastNumber + 1;
                }

                $buy_receipt->pass_number = $prefix.str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
            }
        });

        static::saving(function (BuyReceipt $buy_receipt) {
            $buy_receipt->net_weight = max(0, (int) $buy_receipt->gross_weight - (int) $buy_receipt->tare_weight);
        });
    }
}
