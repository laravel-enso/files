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
            'folders' => VisibleFiles::values(),
            'stats' => [
                'filteredSpaceUsed' => $this->filteredSpaceUsed(),
                'totalSpaceUsed' => $this->totalSpaceUsed($request),
                'storageLimit' => config('enso.files.storageLimit'),
            ],
        ];
    }

    private function filteredSpaceUsed()
    {
        return round(
            $this->collection->sum('size') / 1000
        );
    }

    private function totalSpaceUsed($request)
    {
        return round(
            $request->user()->files()
                ->visible()
                ->sum('size') / 1000
        );
    }
}
