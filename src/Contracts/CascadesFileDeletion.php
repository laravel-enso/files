<?php

namespace LaravelEnso\Files\Contracts;

use LaravelEnso\Files\Models\File;

interface CascadesFileDeletion
{
    public static function cascadeDeletion(File $file): void;
}
