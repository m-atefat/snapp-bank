<?php

namespace App\Rules;

use App\ValueObjects\Card;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class CardNumberRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param Closure(string): PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!Card::isValid($value)){
            $fail('validation.card')->translate();
        }
    }
}
