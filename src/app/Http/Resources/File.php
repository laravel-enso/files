<?php

namespace LaravelEnso\FileManager\app\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use LaravelEnso\TrackWho\app\Http\Resources\TrackWho;

class File extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->original_name,
            'size' => $this->size,
            'mimeType' => $this->mime_type,
            'owner' => new TrackWho($this->whenLoaded('createdBy')),
            'createdAt' => $this->created_at->toDatetimeString(),
        ];
    }
}
