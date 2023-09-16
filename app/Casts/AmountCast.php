<?php

namespace App\Casts;

use App\Exceptions\InvalidAmountException;
use App\ValueObjects\Amount;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;
use Throwable;

class AmountCast implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param Model $model
     * @param string $key
     * @param mixed $value
     * @param array<string, mixed> $attributes
     * @return mixed
     * @throws InvalidAmountException
     * @throws Throwable
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        return Amount::forge($value);
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        if (!$value instanceof Amount){
            throw new InvalidArgumentException('The given value is not an Amount instance.');
        }

        return $value->getAmount();
    }
}
