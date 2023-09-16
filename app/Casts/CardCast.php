<?php

namespace App\Casts;

use App\Exceptions\InvalidCardNumberException;
use App\Exceptions\InvalidCardNumberLengthException;
use App\ValueObjects\Card;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;
use Throwable;

class CardCast implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param Model $model
     * @param string $key
     * @param mixed $value
     * @param array<string, mixed> $attributes
     * @return mixed
     * @throws InvalidCardNumberException
     * @throws InvalidCardNumberLengthException
     * @throws Throwable
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        return Card::fromString($value);
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        if (!$value instanceof Card){
            throw new InvalidArgumentException('The given value is not an Card instance.');
        }

        return $value->toString();
    }
}
