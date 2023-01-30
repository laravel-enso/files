<?php

namespace LaravelEnso\Files\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use LaravelEnso\Helpers\Services\DiskSize;
use LaravelEnso\Users\Http\Resources\User;

class File extends JsonResource
{
    public function toArray($request)
    {
        $accessible = $request->user()->can('access', $this->resource);

        return [
            'id' => $this->id,
            'name' => $this->name(),
            'extension' => $this->extension(),
            'size' => DiskSize::forHumans($this->size),
            'mimeType' => $this->mime_type,
            'type' => new Type($this->whenLoaded('type')),
            'owner' => new User($this->whenLoaded('createdBy')),
            'isFavorite' => $this->relationLoaded('favorite') ? $this->favorite : false,
            'isManageable' => $request->user()->can('manage', $this->resource),
            'isAccessible' => $accessible,
            'isPublic' => $this->is_public,
            'createdAt' => $this->created_at->toDatetimeString(),
        ];
    }
}
