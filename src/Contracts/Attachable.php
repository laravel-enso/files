<?php

namespace LaravelEnso\Files\Contracts;

use Illuminate\Database\Eloquent\Relations\Relation;

interface Attachable
{
    public function file(): Relation;

    public function folder(): string;

    public function mimeTypes(): array;

    public function extensions(): array;

    public function resizeImages(): array;

    public function optimizeImages(): bool;
}
