<?php

use LaravelEnso\Migrator\Database\Migration;

return new class extends Migration {
    protected array $permissions = [
        ['name' => 'core.files.index', 'description' => 'List files', 'is_default' => true],
        ['name' => 'core.files.link', 'description' => 'Get file download temporary link', 'is_default' => true],
        ['name' => 'core.files.show', 'description' => 'Open file in browser', 'is_default' => true],
        ['name' => 'core.files.download', 'description' => 'Download file', 'is_default' => true],
        ['name' => 'core.files.destroy', 'description' => 'Delete file', 'is_default' => true],
        ['name' => 'core.files.browse', 'description' => 'Browse file type', 'is_default' => true],
        ['name' => 'core.files.recent', 'description' => 'Browse recent files', 'is_default' => true],
        ['name' => 'core.files.favorites', 'description' => 'Browse favorites files', 'is_default' => true],
        ['name' => 'core.files.update', 'description' => 'Update file name', 'is_default' => true],
        ['name' => 'core.files.makePublic', 'description' => 'Make file public', 'is_default' => true],
        ['name' => 'core.files.makePrivate', 'description' => 'Make file private', 'is_default' => true],
        ['name' => 'core.files.favorite', 'description' => 'Toggle file as favorite', 'is_default' => true],
    ];

    protected array $menu = [
        'name' => 'Files', 'icon' => 'folder-open', 'route' => 'core.files.index', 'order_index' => 255, 'has_children' => false,
    ];
};
