<?php

namespace App\Http\Resources;

use App\Exceptions\InvalidAmountException;
use App\ValueObjects\Amount;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Throwable;

class TopUserTransactionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array<string, mixed>
     * @throws InvalidAmountException
     * @throws Throwable
     */
    public function toArray(Request $request): array
    {
        return [
            'amount' => $this->resource->amount->decrease(Amount::forge($this->resource->fee_amount ?? 0))->getAmount(),
            'fee_amount' => $this->resource->fee_amount,
            'status' => $this->resource->status,
            'balance' => $this->resource->balance->getAmount(),
            'is_deposit' => $this->resource->is_deposit,
            'type' => $this->resource->type->name,
            'card_number' => $this->resource->card_number,
            'done_at' => $this->resource->done_at->format('Y-m-d H:i:s'),
            'track_id' => $this->resource->track_id,
        ];
    }
}
