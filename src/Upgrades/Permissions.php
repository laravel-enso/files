<?php

namespace LaravelEnso\Files\Upgrades;

use LaravelEnso\Upgrade\Contracts\MigratesStructure;
use LaravelEnso\Upgrade\Traits\StructureMigration;

class Permissions implements MigratesStructure
{
    use StructureMigration;

    protected $permissions = [
        ['name' => 'core.files.favorite', 'description' => 'Toggle file as favorite', 'is_default' => true],
        ['name' => 'core.files.browse', 'description' => 'Browse file type', 'is_default' => true],
        ['name' => 'core.files.recent', 'description' => 'Browse recent files', 'is_default' => true],
        ['name' => 'core.files.favorites', 'description' => 'Browse favorites files', 'is_default' => true],
        ['name' => 'core.files.makePublic', 'description' => 'Make file public', 'is_default' => true],
        ['name' => 'core.files.makePrivate', 'description' => 'Make file private', 'is_default' => true],
    ];
}
