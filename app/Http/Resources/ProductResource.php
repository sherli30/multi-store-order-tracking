<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * ProductResource — Clean JSON representation of a Product.
 *
 * Usage: return new ProductResource($product);
 *        return ProductResource::collection($products);
 */
class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'store_id'        => $this->store_id,
            'category_id'     => $this->category_id,
            'name'            => $this->name,
            'slug'            => $this->slug,
            'description'     => $this->description,
            'price'           => (float) $this->price,
            'formatted_price' => $this->formatted_price,  // accessor
            'stock'           => $this->stock,
            'weight'          => (float) $this->weight,
            'image_url'       => $this->image_url,        // accessor
            'is_active'       => $this->is_active,
            'created_at'      => $this->created_at?->toIso8601String(),
            'updated_at'      => $this->updated_at?->toIso8601String(),

            // Relationships (only when loaded)
            'store'           => new StoreResource($this->whenLoaded('store')),
            'category'        => new CategoryResource($this->whenLoaded('category')),
            'stock_movements' => StockMovementResource::collection($this->whenLoaded('stockMovements')),
        ];
    }
}
