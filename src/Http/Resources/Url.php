<?php

namespace LaravelEnso\Files\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Config;

class Url extends JsonResource
{
    public function toArray($request)
    {
        $appUrl = Config::get('app.url');

        return [
            'id' => $this->id,
            'path' => $this->path(),
            'url' => "{$appUrl}/{$this->path()}",
        ];
    }
}
