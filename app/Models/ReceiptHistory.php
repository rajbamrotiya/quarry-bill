<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReceiptHistory extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'receipt_id',
        'user_id',
        'changes',
        'event',
    ];

    protected $casts = [
        'changes' => 'array',
    ];

    public function receipt(): BelongsTo
    {
        return $this->belongsTo(Receipt::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
