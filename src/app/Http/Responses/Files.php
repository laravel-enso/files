<?php

namespace LaravelEnso\Files\App\Http\Responses;

use Illuminate\Contracts\Support\Responsable;
use LaravelEnso\Files\App\Http\Resources\Collection;
use LaravelEnso\Files\App\Http\Resources\File as Resource;
use LaravelEnso\Files\App\Models\File;

class Files implements Responsable
{
    public function toResponse($request)
    {
        return new Collection(
            Resource::collection(
                File::visible()
                    ->with(['createdBy.avatar', 'attachable'])
                    ->forUser($request->user())
                    ->between(json_decode($request->get('interval')))
                    ->filter($request->get('query'))
                    ->ordered()
                    ->skip($request->get('offset'))
                    ->take(config('enso.files.paginate'))
                    ->get()
                )
            );
    }
}
