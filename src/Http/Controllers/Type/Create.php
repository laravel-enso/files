<?php

namespace LaravelEnso\Files\Http\Controllers\Type;

use Illuminate\Routing\Controller;
use LaravelEnso\Files\Forms\Builders\TypeForm;

class Create extends Controller
{
    public function __invoke(TypeForm $form)
    {
        return ['form' => $form->create()];
    }
}
