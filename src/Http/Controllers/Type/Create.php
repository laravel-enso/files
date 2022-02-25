<?php

namespace LaravelEnso\Files\Http\Controllers\Type;

use Illuminate\Routing\Controller;
use LaravelEnso\Files\Forms\Builders\Type;

class Create extends Controller
{
    public function __invoke(Type $form)
    {
        return ['form' => $form->create()];
    }
}
