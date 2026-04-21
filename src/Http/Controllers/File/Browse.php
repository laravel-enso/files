<?php

namespace LaravelEnso\Files\Http\Controllers\File;

use Illuminate\Routing\Controller;
use LaravelEnso\Files\Http\Requests\Browse as ValidateBrowse;
use LaravelEnso\Files\Http\Resources\Files;
use LaravelEnso\Files\Models\Type;

class Browse extends Controller
{
    public function __invoke(ValidateBrowse $request, Type $type)
    {
        $files = $type->files()
            ->for($request->user())
            ->between($request->input('interval'))
            ->filter($request->string('query'))
            ->latest('id')
            ->paginate($request->integer('pagination'));

        return new Files($files);
    }
}
