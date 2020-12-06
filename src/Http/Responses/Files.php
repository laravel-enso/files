<?php

namespace LaravelEnso\Files\Http\Responses;

use Illuminate\Contracts\Support\Responsable;
use Illuminate\Support\Facades\Config;
use LaravelEnso\Files\Http\Resources\Collection;
use LaravelEnso\Files\Http\Resources\File as Resource;
use LaravelEnso\Files\Models\File;

class Files implements Responsable
{
    public function toResponse($request)
    {
        return new Collection(
            Resource::collection(
                File::latest()->browsable()
                    ->with(['createdBy.avatar', 'attachable'])
                    ->for($request->user())
                    ->between(json_decode($request->get('interval'), true))
                    ->filter($request->get('query'))
                    ->skip($request->get('offset'))
                    ->take(Config::get('enso.files.paginate'))
                    ->get()
            )
        );
    }
}
