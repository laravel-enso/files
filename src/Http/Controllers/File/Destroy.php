<?php

namespace LaravelEnso\Files\Http\Controllers\File;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use LaravelEnso\Files\Models\File;

class Destroy extends Controller
{
    use AuthorizesRequests;

    public function __invoke(File $file)
    {
        $this->authorize('destroy', $file);

        DB::transaction(fn () => $file->delete(true));
    }
}
