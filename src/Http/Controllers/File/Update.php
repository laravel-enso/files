<?php

namespace LaravelEnso\Files\Http\Controllers\File;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Routing\Controller;
use LaravelEnso\Files\Http\Requests\ValidateName;
use LaravelEnso\Files\Models\File;

class Update extends Controller
{
    use AuthorizesRequests;

    public function __invoke(ValidateName $request, File $file)
    {
        $this->authorize('manage', $file);

        $name = "{$request->get('name')}.{$file->extension()}";

        $file->update(['original_name' => $name]);
    }
}
