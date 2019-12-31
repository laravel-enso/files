<?php

use LaravelEnso\Migrator\App\Database\Migration;
use LaravelEnso\Permissions\App\Enums\Types;

class CreateStructureForFiles extends Migration
{
    protected $permissions = [
        ['name' => 'core.files.index', 'description' => 'List files', 'type' => Types::Read, 'is_default' => true],
        ['name' => 'core.files.link', 'description' => 'Get file download temporary link', 'type' => Types::Read, 'is_default' => true],
        ['name' => 'core.files.show', 'description' => 'Open file in browser', 'type' => Types::Read, 'is_default' => true],
        ['name' => 'core.files.download', 'description' => 'Download file', 'type' => Types::Read, 'is_default' => true],
        ['name' => 'core.files.destroy', 'description' => 'Delete file', 'type' => Types::Write, 'is_default' => true],
    ];

    protected $menu = [
        'name' => 'Files', 'icon' => 'folder-open', 'route' => 'core.files.index', 'order_index' => 255, 'has_children' => false,
    ];
}
