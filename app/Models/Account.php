<?php

namespace App\Models;

use App\Casts\AmountCast;
use App\ValueObjects\Amount;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property Amount $balance
 * @property-read  User|null $user
 */
class Account extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'balance' => AmountCast::class
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function cards(): HasMany
    {
        return $this->hasMany(Card::class);
    }

    public function scopeWhereCardNumberIs(Builder $builder, \App\ValueObjects\Card $card): Builder
    {
        return $builder->whereHas('cards', function(Builder $query) use ($card) {
            $query->where('number', $card->toString());
        });
    }
}
