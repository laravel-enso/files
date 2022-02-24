<?php

namespace LaravelEnso\Files\Forms\Builders;

use LaravelEnso\Files\Models\Type;
use LaravelEnso\Forms\Services\Form;

class TypeForm
{
    protected const FormPath = __DIR__.'/../Templates/type.json';

    protected Form $form;

    public function __construct()
    {
        $this->form = (new Form(static::FormPath));
    }

    public function create()
    {
        return $this->form->create();
    }

    public function edit(Type $type)
    {
        return $this->form->edit($type);
    }
}
