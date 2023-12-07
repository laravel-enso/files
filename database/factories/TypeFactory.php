<?php

namespace LaravelEnso\Files\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use LaravelEnso\Files\Models\Type;

class TypeFactory extends Factory
{
    protected $model = Type::class;

    public function definition()
    {
        return [
            'name' => null,
            'folder' => 'null',
            'model' => null,
            'icon' => 'folder',
            'endpoint' => null,
            'description' => null,
            'is_public' => false,
            'is_browsable' => false,
            'is_system' => false,
        ];
    }

    public function model(string $model): self
    {
        $name = Str::of($model)->afterLast('\\')
            ->snake()
            ->replace('_', ' ')
            ->title()
            ->plural();

        return $this->state(fn () => [
            'name' => $name,
            'folder' => $name->camel(),
            'model' => $model,
            'description' => "Enso {$name}",
        ]);
    }
}
