<?php

namespace App\Services\ReportServices;

use App\Enums\TransactionStatus;
use App\Enums\TransactionType;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ReportServices
{
    public function topUserInCardToCardTransaction(int $userCount = 3, int $transaction_count = 10)
    {
        return User::query()
            ->join('accounts', 'users.id', '=', 'accounts.user_id')
            ->join('cards', 'accounts.id', '=', 'cards.account_id')
            ->join('transactions', 'cards.id', '=', 'transactions.card_id')
            ->where('transactions.done_at', '>=', Carbon::now()->subMinutes(10))
            ->where('transactions.status', TransactionStatus::DONE->value)
            ->where('transactions.type', TransactionType::CARD_TO_CARD)
            ->where('transactions.is_deposit', false)
            ->groupBy('users.id')
            ->orderByDesc(DB::raw('COUNT(transactions.id)'))
            ->select('users.id', 'users.name', 'users.mobile', DB::raw('COUNT(transactions.id) transactions_count'))
            ->take($userCount)
            ->get()->each(function (User &$user) use ($transaction_count) {
                $transactions = Transaction::query()
                    ->join('cards', 'transactions.card_id', '=', 'cards.id')
                    ->join('accounts', 'cards.account_id', '=', 'accounts.id')
                    ->join('users', 'accounts.user_id', '=', 'users.id')
                    ->leftJoin('fees', 'fees.transaction_id', '=', 'transactions.id')
                    ->where('users.id', $user->id)
                    ->where('transactions.status', TransactionStatus::DONE)
                    ->orderBy('transactions.created_at', 'desc')
                    ->select(
                        DB::raw('cards.number card_number'),
                        'transactions.amount',
                        'transactions.balance',
                        'transactions.is_deposit',
                        'transactions.status',
                        'transactions.done_at',
                        'transactions.track_id',
                        'transactions.type',
                        DB::raw('fees.amount fee_amount')
                    )
                    ->take($transaction_count)
                    ->get();

                $user->transactions = $transactions;
            });
    }
}
