<?php

namespace LaravelEnso\Files\app\Contracts;

interface VisibleFile
{
    public function isDeletable(): bool;
}
