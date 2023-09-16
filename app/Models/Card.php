<?php

namespace App\Models;

use App\Casts\CardCast;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\ValueObjects\Card as CardValueObject;

/**
 * @method static Builder findWithNumber(CardValueObject $card)
 * @property \App\ValueObjects\Card $number
 * @property-read Account|null $account
 */
class Card extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $hidden = ['password'];

    protected $casts = [
        'number' => CardCast::class
    ];

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function scopeFindWithNumber(Builder $builder, CardValueObject $card): Builder
    {
        return $builder->where('number', $card->toString());
    }
}
