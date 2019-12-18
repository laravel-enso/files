<?php

use LaravelEnso\Migrator\app\Database\Migration;

class CreateStructureForFiles extends Migration
{
    protected $permissions = [
        ['name' => 'core.files.index', 'description' => 'List files', 'type' => 0, 'is_default' => true],
        ['name' => 'core.files.link', 'description' => 'Get file download temporary link', 'type' => 0, 'is_default' => true],
        ['name' => 'core.files.show', 'description' => 'Open file in browser', 'type' => 0, 'is_default' => true],
        ['name' => 'core.files.download', 'description' => 'Download file', 'type' => 0, 'is_default' => true],
        ['name' => 'core.files.destroy', 'description' => 'Delete file', 'type' => 1, 'is_default' => true],
    ];

    protected $menu = [
        'name' => 'Files', 'icon' => 'folder-open', 'route' => 'core.files.index', 'order_index' => 255, 'has_children' => false,
    ];
}
