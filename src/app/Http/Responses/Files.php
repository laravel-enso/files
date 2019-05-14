<?php

namespace LaravelEnso\Files\app\Http\Responses;

use LaravelEnso\Files\app\Models\File;
use Illuminate\Contracts\Support\Responsable;
use LaravelEnso\Files\app\Http\Resources\Collection;
use LaravelEnso\Files\app\Http\Resources\File as Resource;

class Files implements Responsable
{
    public function toResponse($request)
    {
        return new Collection(
            Resource::collection(
                File::visible()
                    ->with(['createdBy', 'attachable'])
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