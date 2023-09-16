<?php

namespace App\ValueObjects;

use App\Exceptions\InvalidAmountException;
use Throwable;

class Amount
{
    private function __construct(private int|float $amount)
    {
    }

    /**
     * @throws Throwable
     * @throws InvalidAmountException
     */
    public static function forge(int|float $amount): self
    {
        static::validate($amount);
        return new self($amount);
    }

    /**
     * @throws Throwable
     * @throws InvalidAmountException
     */
    private static function validate(int|float $amount): void
    {
        throw_if($amount < 0, InvalidAmountException::class);
    }

    public function getAmount(): int|float
    {
        return $this->amount;
    }

    public function increase(Amount $amount): self
    {
        return new self($this->getAmount() + $amount->getAmount());
    }

    public function decrease(Amount $amount): self
    {
        return new self($this->getAmount() - $amount->getAmount());
    }

    public function isLowerThan(Amount $amount): bool
    {
        return $this->getAmount() < $amount->getAmount();
    }
}
