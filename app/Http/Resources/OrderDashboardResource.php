<?php

namespace App\Http\Resources;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderDashboardResource extends JsonResource
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
            'ordered_products' => $this->getOrderedProducts(json_decode($this->resource->products)),
            'customer' => $this->resource->user->fullName,
            'status' => $this->resource->orderStatus->title,
            'amount' => $this->resource->amount,
            'uuid' => $this->resource->uuid,
        ];
    }

    private function getOrderedProducts(array $products): int
    {
        $count = 0;
        foreach ($products as $product) {
            $count += $product->quantity;
        }

        return $count;
    }
}
