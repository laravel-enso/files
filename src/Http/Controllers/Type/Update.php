<?php

namespace LaravelEnso\Files\Http\Controllers\Type;

use Illuminate\Routing\Controller;
use LaravelEnso\Files\Http\Requests\ValidateType;
use LaravelEnso\Files\Models\Type;

class Update extends Controller
{
    public function __invoke(ValidateType $request, Type $type)
    {
        $type->update($request->validated());

        return ['message' => __('The file type was successfully updated')];
    }
}
