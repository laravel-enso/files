<?php

namespace LaravelEnso\Files\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;
use LaravelEnso\Files\Models\Upload;

class Type extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => Str::title($this->name),
            'icon' => $this->icon,
            'endpoint' => $this->endpoint,
            'isBrowsable' => $this->is_browsable,
            'isSystem' => $this->is_system,
            'isUpload' => $this->model === Upload::class,
        ];
    }
}
