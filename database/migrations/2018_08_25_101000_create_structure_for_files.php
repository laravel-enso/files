<?php

use LaravelEnso\Migrator\Database\Migration;

class CreateStructureForFiles extends Migration
{
    protected array $permissions = [
        ['name' => 'core.files.index', 'description' => 'List files', 'is_default' => true],
        ['name' => 'core.files.link', 'description' => 'Get file download temporary link', 'is_default' => true],
        ['name' => 'core.files.show', 'description' => 'Open file in browser', 'is_default' => true],
        ['name' => 'core.files.download', 'description' => 'Download file', 'is_default' => true],
        ['name' => 'core.files.destroy', 'description' => 'Delete file', 'is_default' => true],
        ['name' => 'core.files.favorite', 'description' => 'Toggle file as favorite', 'is_default' => true],
        ['name' => 'core.files.browse', 'description' => 'Browse file type', 'is_default' => true],
        ['name' => 'core.files.recent', 'description' => 'Browse recent files', 'is_default' => true],
        ['name' => 'core.files.favorites', 'description' => 'Browse favorites files', 'is_default' => true],
        ['name' => 'core.files.sharedWithYou', 'description' => 'Browse files shared with user', 'is_default' => true],
        ['name' => 'core.files.sharedByYou', 'description' => 'Browse files shared by user', 'is_default' => true],
        ['name' => 'core.files.favorite', 'description' => 'Toggle file as favorite', 'is_default' => true],
    ];

    protected array $menu = [
        'name' => 'Files', 'icon' => 'folder-open', 'route' => 'core.files.index', 'order_index' => 255, 'has_children' => false,
    ];
}
