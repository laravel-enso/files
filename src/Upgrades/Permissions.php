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
        ['name' => 'core.files.sharedWithYou', 'description' => 'Browse files shared with user', 'is_default' => true],
        ['name' => 'core.files.sharedByYou', 'description' => 'Browse files shared by user', 'is_default' => true],
    ];
}
