<?php

namespace LaravelEnso\Files\Http\Controllers\Type;

use Illuminate\Routing\Controller;
use LaravelEnso\Files\Forms\Builders\Type;
use LaravelEnso\Files\Models\Type as Model;

class Edit extends Controller
{
    public function __invoke(Model $type, Type $form)
    {
        return ['form' => $form->edit($type)];
    }
}
