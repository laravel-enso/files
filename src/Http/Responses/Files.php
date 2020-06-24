<?php

namespace LaravelEnso\Files\Http\Responses;

use Illuminate\Contracts\Support\Responsable;
use LaravelEnso\Files\Http\Resources\Collection;
use LaravelEnso\Files\Http\Resources\File as Resource;
use LaravelEnso\Files\Models\File;

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
