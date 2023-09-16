<?php

namespace App\Models;

use App\Casts\AmountCast;
use App\ValueObjects\Amount;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property Amount $amount
 */
class Fee extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'amount' => AmountCast::class
    ];
}
