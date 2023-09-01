<?php

namespace App\Http\Resources;

use App\Models\Order;
use App\Models\Product;
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
            'categories' => $this->getProductCategories(json_decode($this->resource->products)),
            'address' => json_decode($this->resource->address),
            'amount' => $this->resource->amount,
            'uuid' => $this->resource->uuid,
            'shipped_at' => $this->resource->shipped_at
        ];
    }

    private function getProductCategories($products): array
    {
        $categories = [];
        foreach ($products as $product){
            $categories[] = Product::find($product->product)->category->title;
        }

        return $categories;
    }
}
