<?php

namespace App\Http\Controllers\Api\V1;

use App\Exceptions\AccountBalanceInsufficientException;
use App\Exceptions\CardToCardAmountLimitationException;
use App\Exceptions\InvalidCardNumberException;
use App\Exceptions\InvalidCardNumberLengthException;
use App\Http\Requests\CardToCardRequest;
use App\Services\CardServices\CardServices;
use App\ValueObjects\Card;
use App\ValueObjects\CardToCardAmount;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Throwable;

class CardController extends Controller
{

    public function __construct(private readonly CardServices $cardServices)
    {
    }

    /**
     * @throws Throwable
     * @throws InvalidCardNumberException
     * @throws CardToCardAmountLimitationException
     * @throws InvalidCardNumberLengthException
     */
    public function cardToCard(CardToCardRequest $request): JsonResponse
    {
        $sourceCard = Card::fromString($request->input('source_card_number'));
        $destCard = Card::fromString($request->input('destination_card_number'));
        $amount = CardToCardAmount::forge($request->input('amount'));

        try {

            $result = $this->cardServices->cardToCard($sourceCard, $destCard, $amount);
            return response()->json([
                'status' => $result['status'] === true ? 'success' : 'failed',
                'error' => $result['error'],
                'tracking_code' => $result['withdraw_transaction']?->track_id
            ]);

        } catch (AccountBalanceInsufficientException $insufficientException) {
            return response()->json([
                'status' => 'failed',
                'error' => CardServices::INSUFFICIENT_BALANCE,
                'tracking_code' => null
            ]);
        }
    }
}
