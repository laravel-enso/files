<?php

namespace LaravelEnso\Files\Http\Controllers\Type;

use Illuminate\Routing\Controller;
use LaravelEnso\Files\Forms\Builders\TypeForm;
use LaravelEnso\Files\Models\Type;

class Edit extends Controller
{
    public function __invoke(Type $type, TypeForm $form)
    {
        return ['form' => $form->edit($type)];
    }
}
