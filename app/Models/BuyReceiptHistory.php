<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BuyReceiptHistory extends Model
{
    protected $fillable = [
        'buy_receipt_id',
        'user_id',
        'changes',
        'event',
    ];

    protected $casts = [
        'changes' => 'array',
    ];

    public function buy_receipt(): BelongsTo
    {
        return $this->belongsTo(BuyReceipt::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
