<?php

namespace App\Services\ReportServices;

use App\Enums\TransactionType;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ReportServices
{
    public function topUserInCardToCardTransaction(int $userCount = 3, int $transaction_count = 10): Collection
    {
        $time = now()->subMinutes(10);
        $topUsersWithTransactions = DB::select("
        WITH recent_transactions AS (
    SELECT
        u.id user_id,
        u.mobile user_mobile,
        t.id,
        t.done_at,
        t.amount,
        t.status,
        t.type,
        t.is_deposit,
        t.track_id,
        f.amount fee_amount,
        ROW_NUMBER() OVER (PARTITION BY u.id ORDER BY t.done_at DESC) AS RowNum
    FROM
        users u
    INNER JOIN
        accounts a ON u.id = a.user_id
    INNER JOIN
        cards c ON a.id = c.account_id
    INNER JOIN
        transactions t ON c.id = t.card_id
    INNER JOIN
        fees f ON t.id = f.transaction_id
    WHERE
        t.done_at >= ? and t.is_deposit = 0 and t.type = " . TransactionType::CARD_TO_CARD->value . "
),
top_users AS (
    SELECT
        user_id
    FROM
        recent_transactions
    WHERE
        RowNum <= 10
    GROUP BY
        user_id
    ORDER BY
        COUNT(*) DESC
    LIMIT 3
)
SELECT
    u.id user_id,
    u.mobile user_mobile,
    u.name user_name,
    t.id transaction_id,
    t.done_at,
    t.status,
    t.amount,
    t.type,
    t.track_id,
    t.RowNum,
    t.is_deposit,
    t.fee_amount
FROM
    users u
INNER JOIN
    recent_transactions t ON u.id = t.user_id
WHERE
    u.id IN (SELECT user_id FROM top_users)
    and t.RowNum <= 10
ORDER BY
    u.id,
    t.done_at DESC;
        ", [$time]);

        return collect($topUsersWithTransactions)->groupBy('user_id');
    }
}
