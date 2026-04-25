<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * CategoryResource — Clean JSON representation of a ProductCategory.
 *
 * Usage: return new CategoryResource($category);
 *        return CategoryResource::collection($categories);
 */
class CategoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'store_id'    => $this->store_id,
            'name'        => $this->name,
            'slug'        => $this->slug,
            'description' => $this->description,
            'is_active'   => $this->is_active,
            'created_at'  => $this->created_at?->toIso8601String(),

            // Relationships (only when loaded)
            'store'       => new StoreResource($this->whenLoaded('store')),
            'products'    => ProductResource::collection($this->whenLoaded('products')),
        ];
    }
}
