<?php

namespace App\Http\Resources;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function __construct(Order $resource)
    {
        parent::__construct($resource);
    }

    /**
     * @return array<string, array<string>|string>
     */
    public function toArray(Request $request): array
    {
        return [
            'products' => json_decode($this->resource->products),
            'address' => json_decode($this->resource->address),
            'user' => new MiniUserResource($this->resource->user),
            'amount' => $this->resource->amount,
            'uuid' => $this->resource->uuid,
            'created_at' => $this->resource->created_at,
        ];
    }

}
