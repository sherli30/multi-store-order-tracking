<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * StockMovementResource — Clean JSON representation of a StockMovement.
 *
 * Usage: return new StockMovementResource($movement);
 *        return StockMovementResource::collection($movements);
 */
class StockMovementResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'product_id'   => $this->product_id,
            'type'         => $this->type,                  // 'in' | 'out'
            'type_label'   => $this->type_label,            // 'Masuk' | 'Keluar'
            'quantity'     => $this->quantity,
            'source'       => $this->source,
            'source_label' => $this->source_label,          // Human-readable accessor
            'reference_id' => $this->reference_id,
            'created_at'   => $this->created_at?->toIso8601String(),

            // Relationship (only when loaded)
            'product'      => new ProductResource($this->whenLoaded('product')),
        ];
    }
}
