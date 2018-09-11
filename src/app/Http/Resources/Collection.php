<?php

namespace LaravelEnso\FileManager\app\Http\Resources;

use LaravelEnso\FileManager\app\Enums\VisibleFiles;
use Illuminate\Http\Resources\Json\ResourceCollection;

class Collection extends ResourceCollection
{
    public function toArray($request)
    {
        return [
            'data' => $this->collection,
            'types' => VisibleFiles::values(),
        ];
    }
}
