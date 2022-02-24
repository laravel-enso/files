<?php

namespace LaravelEnso\Files\Contracts;

use Illuminate\Database\Eloquent\Relations\Relation;

interface Attachable
{
    public function file(): Relation;
}
