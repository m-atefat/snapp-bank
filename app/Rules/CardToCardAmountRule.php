<?php

namespace App\Rules;

use App\ValueObjects\Amount;
use App\ValueObjects\CardToCardAmount;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;
use Throwable;

class CardToCardAmountRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param Closure(string): PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $min_and_max_amount_for_card_to_card = [
            'min_amount' => CardToCardAmount::MINIMUM_CARD_TO_CARD_AMOUNT,
            'max_amount' => CardToCardAmount::MAXIMUM_CARD_TO_CARD_AMOUNT
        ];

        try {
            if (!CardToCardAmount::isValid(Amount::forge($value))) {
                $fail('validation.card_to_card_amount')->translate($min_and_max_amount_for_card_to_card);
            }
        } catch (Throwable $exception) {
            $fail('validation.card_to_card_amount')->translate($min_and_max_amount_for_card_to_card);
        }
    }
}
