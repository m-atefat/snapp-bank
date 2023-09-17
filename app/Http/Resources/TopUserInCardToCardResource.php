<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TopUserInCardToCardResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->first()?->user_id,
            'name' => $this->resource->first()?->user_name,
            'mobile' => $this->resource->first()?->user_mobile,
            'transactions' => TopUserTransactionResource::collection($this->resource)
        ];
    }
}
