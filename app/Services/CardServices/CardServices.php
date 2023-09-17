<?php

namespace App\Services\CardServices;

use App\Enums\TransactionStatus;
use App\Enums\TransactionType;
use App\Events\CardToCardDoneEvent;
use App\Exceptions\AccountBalanceInsufficientException;
use App\Exceptions\InvalidAmountException;
use App\Models\Account;
use App\Models\Card as CardModel;
use App\Models\Transaction;
use App\Services\TransactionServices\TransactionServices;
use App\ValueObjects\Amount;
use App\ValueObjects\Card;
use App\ValueObjects\CardToCardAmount;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class CardServices
{
    public const INSUFFICIENT_BALANCE = 'INSUFFICIENT_BALANCE';

    public const TRANSACTION_FAILED = 'TRANSACTION_FAILED';

    public function __construct(private TransactionServices $transactionServices)
    {
    }

    /**
     * @throws Throwable
     * @throws AccountBalanceInsufficientException
     * @throws InvalidAmountException
     */
    public function cardToCard(Card $sourceCard, Card $destinationCard, CardToCardAmount $amount): array
    {
        $depositTransaction = null;
        $status = false;
        $error = null;

        $fee = Amount::forge(config('fee.card_to_card'));
        $amountWithFee = $amount->getAmountObject()->increase($fee);

        /** @var CardModel $sourceCardModel */
        $sourceCardModel = CardModel::findWithNumber($sourceCard)->with('account')->firstOrFail();

        /** @var CardModel $destinationCardModel */
        $destinationCardModel = CardModel::findWithNumber($destinationCard)->firstOrFail();

        if ($sourceCardModel->account->balance->isLowerThan($amountWithFee)) {
            throw new AccountBalanceInsufficientException('Account Balance Insufficient.');
        }

        /** @var Transaction $withdrawTransaction */
        $withdrawTransaction = $this->transactionServices->createWithdrawTransaction(
            $sourceCardModel,
            $amountWithFee,
            TransactionType::CARD_TO_CARD
        );

        try {
            DB::beginTransaction();

            $sourceAccount = $this->findAccount($sourceCardModel);
            $destinationAccount = $this->findAccount($destinationCardModel);

            $sourceAccount->update([
                'balance' => $sourceAccount->balance->decrease($amountWithFee)
            ]);

            $destinationAccount->update([
                'balance' => $destinationAccount->fresh()->balance->increase($amount->getAmountObject())
            ]);
            $this->transactionServices->setTransactionDone($withdrawTransaction);

            $depositTransaction = $this->transactionServices->createDepositTransaction(
                $destinationCardModel,
                $amount->getAmountObject(),
                TransactionType::CARD_TO_CARD,
                $withdrawTransaction
            );

            $this->transactionServices->setTransactionDone($depositTransaction);
            $this->transactionServices->createFeeTransaction($withdrawTransaction, $fee);

            $status = true;
            DB::commit();
        } catch (Exception $exception) {
            DB::rollBack();

            Log::critical('card to card failed', [
                'exception' => get_class($exception),
                'exception_message' => $exception->getMessage(),
                'source' => $sourceCard->toString(),
                'destination' => $destinationCard->toString(),
                'amount' => $amountWithFee->getAmount()
            ]);

            $withdrawTransaction = $this->transactionServices->setTransactionFailed($withdrawTransaction);
            $error = self::TRANSACTION_FAILED;
        }

        if ($status){
            CardToCardDoneEvent::dispatch([
                'withdraw_transaction' => $withdrawTransaction,
                'deposit_transaction' => $depositTransaction
            ]);
        }

        return [
            'status' => $status,
            'error' => $error,
            'withdraw_transaction' => $withdrawTransaction,
            'deposit_transaction' => $depositTransaction,
        ];
    }

    private function findAccount(CardModel $card): Account|Model
    {
        return $card->account()->lockForUpdate()->firstOrFail();
    }
}
