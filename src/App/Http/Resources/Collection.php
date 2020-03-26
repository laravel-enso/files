<?php

namespace LaravelEnso\Files\App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;
use LaravelEnso\Files\App\Facades\FileBrowser;

class Collection extends ResourceCollection
{
    public function toArray($request)
    {
        return [
            'data' => $this->collection,
            'folders' => FileBrowser::folders(),
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
