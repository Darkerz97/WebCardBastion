<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'name' => $this->name,
            'slug' => $this->slug,
            'sku' => $this->sku,
            'barcode' => $this->barcode,
            'description' => $this->description,
            'short_description' => $this->short_description,
            'category' => $this->category,
            'category_id' => $this->category_id,
            'category_model' => $this->whenLoaded('categoryModel', fn () => [
                'id' => $this->categoryModel?->id,
                'name' => $this->categoryModel?->name,
                'slug' => $this->categoryModel?->slug,
            ]),
            'cost' => (float) $this->cost,
            'price' => (float) $this->price,
            'stock' => $this->stock,
            'image_path' => $this->image_path,
            'primary_image_url' => $this->primary_image_url,
            'active' => $this->active,
            'featured' => $this->featured,
            'publish_to_store' => $this->publish_to_store,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
            'deleted_at' => $this->deleted_at?->toIso8601String(),
        ];
    }
}
