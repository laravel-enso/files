<?php

namespace LaravelEnso\Files\Tables\Builders;

use Illuminate\Database\Eloquent\Builder;
use LaravelEnso\Files\Models\Type;
use LaravelEnso\Tables\Contracts\Table;

class TypeTable implements Table
{
    protected const TemplatePath = __DIR__.'/../Templates/types.json';

    public function query(): Builder
    {
        return Type::selectRaw('id, name, icon, folder, model, is_browsable, is_system');
    }

    public function templatePath(): string
    {
        return static::TemplatePath;
    }
}
