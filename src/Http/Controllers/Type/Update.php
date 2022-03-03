<?php

namespace LaravelEnso\Files\Http\Controllers\Type;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use LaravelEnso\Files\Http\Requests\ValidateType;
use LaravelEnso\Files\Models\Type;

class Update extends Controller
{
    public function __invoke(ValidateType $request, Type $type)
    {
        $type->fill($request->validated());

        if ($type->isDirty('folder')) {
            $from = Storage::path($type->getOriginal('folder'));
            $to = Storage::path($type->folder);
            rename($from, $to);
        }

        $type->save();

        return ['message' => __('The file type was successfully updated')];
    }
}
