<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PosSiteSettingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'site_name' => $this->site_name,
            'site_tagline' => $this->site_tagline,
            'announcement_text' => $this->announcement_text,
            'catalog_heading' => $this->catalog_heading,
            'catalog_description' => $this->catalog_description,
            'sync_version' => $this->sync_version,
            'is_active' => true,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
            'deleted_at' => null,
        ];
    }
}
