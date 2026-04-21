<?php

namespace LaravelEnso\Files\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class Files extends ResourceCollection
{
    public $collects = File::class;

    public function paginationInformation($request, $paginated, $default): array
    {
        return [
            'meta' => $default['meta'],
        ];
    }
}
