<?php

namespace LaravelEnso\Files\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;
use LaravelEnso\Helpers\Services\DiskSize;
use LaravelEnso\Users\Http\Resources\User;

class File extends JsonResource
{
    public function toArray($request)
    {
        $accessible = $request->user()->can('access', $this->resource);

        return [
            'id' => $this->id,
            'name' => $this->original_name,
            'size' => DiskSize::forHumans($this->size),
            'mimeType' => $this->mime_type,
            'type' => new Type($this->whenLoaded('type')),
            'owner' => new User($this->whenLoaded('createdBy')),
            'isFavorite' => (bool) $this->whenLoaded('favorite'),
            'isManageable' => $request->user()->can('manage', $this->resource),
            'isAccessible' => $accessible,
            'isViewable' => $accessible && $this->isImage(),
            'isPublic' => $this->is_public,
            'createdAt' => $this->created_at->toDatetimeString(),
        ];
    }

    private function isImage(): bool
    {
        $mimeType = Str::of($this->mime_type);

        return $mimeType->startsWith('image')
            || $mimeType->is('application/pdf');
    }
}
