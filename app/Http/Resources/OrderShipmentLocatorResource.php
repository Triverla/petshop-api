<?php

namespace App\Http\Resources;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderShipmentLocatorResource extends JsonResource
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
            'categories' => implode(', ', $this->getProductCategories(json_decode($this->resource->products))),
            'address' => json_decode($this->resource->address),
            'customer' => $this->resource->user->fullName,
            'amount' => $this->resource->amount,
            'uuid' => $this->resource->uuid,
            'shipped_at' => $this->resource->shipped_at,
        ];
    }

    private function getProductCategories(array $products): array
    {
        $categories = [];
        foreach ($products as $product) {
            $singleProduct = Product::find($product->product);
            if(!$singleProduct){
                continue;
            }
            $categories[] = $singleProduct->category->title;
        }

        return $categories;
    }
}
