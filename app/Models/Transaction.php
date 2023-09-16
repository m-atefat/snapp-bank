<?php

namespace App\Models;

use App\Casts\AmountCast;
use App\Enums\TransactionStatus;
use App\Enums\TransactionType;
use App\ValueObjects\Amount;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

/**
 * @property TransactionStatus $status
 * @property TransactionType $type
 * @property Amount $amount
 * @property Carbon $done_at
 * @property Card $card
 * @property Fee $fee
 */
class Transaction extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'status' => TransactionStatus::class,
        'type' => TransactionType::class,
        'amount' => AmountCast::class,
        'done_at' => 'datetime'
    ];

    public function card(): BelongsTo
    {
        return $this->belongsTo(Card::class);
    }

    public function sourceTransaction(): BelongsTo
    {
        return $this->belongsTo(__CLASS__,'source_transaction_id','id');
    }

    public function fee(): HasOne
    {
        return $this->hasOne(Fee::class, 'transaction_id');
    }
}
