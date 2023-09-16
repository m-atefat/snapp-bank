<?php

namespace App\Services\TransactionServices;

use App\Enums\TransactionStatus;
use App\Enums\TransactionType;
use App\Models\Card;
use App\Models\Fee;
use App\Models\Transaction;
use App\ValueObjects\Amount;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class TransactionServices
{
    public function createWithdrawTransaction(
        Card            $card,
        Amount          $amount,
        TransactionType $transactionType
    ): Transaction|Model
    {
        return $card->transactions()->create([
            'amount' => $amount,
            'type' => $transactionType,
            'status' => TransactionStatus::INIT,
            'track_id' => Str::uuid()->toString(),
        ]);
    }

    public function createDepositTransaction(
        Card            $card,
        Amount          $amount,
        TransactionType $transactionType,
        Transaction     $sourceTransaction = null
    ): Transaction|Model
    {
        return $card->transactions()->create([
            'amount' => $amount,
            'source_transaction_id' => $sourceTransaction?->id,
            'is_deposit' => true,
            'type' => $transactionType,
            'status' => TransactionStatus::INIT,
            'track_id' => Str::uuid()->toString(),
        ]);
    }

    public function setTransactionFailed(Transaction $transaction): Transaction|Model
    {
        $transaction->update(['status' => TransactionStatus::FAILED]);
        return $transaction->refresh();
    }

    public function setTransactionDone(Transaction $transaction): Transaction|Model
    {
        $transaction->update(['status' => TransactionStatus::DONE, 'done_at' => now()]);
        return $transaction->refresh();
    }

    public function createFeeTransaction(Transaction $transaction, Amount $fee): Fee|Model
    {
        return $transaction->fee()->create(['amount' => $fee]);
    }
}
