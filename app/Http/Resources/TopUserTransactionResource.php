<?php

namespace App\Http\Resources;

use App\Enums\TransactionType;
use Carbon\Carbon;
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
     * @throws Throwable
     */
    public function toArray(Request $request): array
    {
        return [
            'amount' => $this->resource->amount - $this->resource->fee_amount,
            'fee_amount' => $this->resource->fee_amount,
            'status' => $this->resource->status,
            'is_deposit' => (bool) $this->resource->is_deposit,
            'type' => TransactionType::from($this->resource->type)->name,
            'done_at' => Carbon::parse($this->resource->done_at)->format('Y-m-d H:i:s'),
            'track_id' => $this->resource->track_id,
        ];
    }
}
