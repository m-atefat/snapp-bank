<?php

namespace App\Http\Requests;

use App\Exceptions\CardToCardAmountLimitationException;
use App\Exceptions\InvalidCardNumberException;
use App\Exceptions\InvalidCardNumberLengthException;
use App\Helpers\Normalizer;
use App\Rules\CardNumberRule;
use App\Rules\CardToCardAmountRule;
use App\ValueObjects\Amount;
use App\ValueObjects\Card;
use App\ValueObjects\CardToCardAmount;
use Illuminate\Foundation\Http\FormRequest;
use Throwable;

class CardToCardRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'source_card_number' => ['required', 'string', new CardNumberRule(), 'exists:cards,number'],
            'destination_card_number' => ['required', 'string', new CardNumberRule(), 'exists:cards,number', 'different:source_card_number'],
            'amount' => ['required', 'numeric', new CardToCardAmountRule()]
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'source_card_number' => Normalizer::toEnglish($this->source_card_number),
            'destination_card_number' => Normalizer::toEnglish($this->destination_card_number),
            'amount' => Normalizer::toEnglish($this->amount)
        ]);
    }
}
