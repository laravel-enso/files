<?php

use LaravelEnso\StructureManager\app\Classes\StructureMigration;

class CreateStructureForFiles extends StructureMigration
{
    protected $permissionGroup = [
        'name' => 'core.files', 'description' => 'Files permissions group',
    ];

    protected $permissions = [
        ['name' => 'core.files.index', 'description' => 'List files', 'type' => 0, 'is_default' => false],
        ['name' => 'core.files.link', 'description' => 'Get file download temporary link', 'type' => 0, 'is_default' => false],
        ['name' => 'core.files.show', 'description' => 'Open file in browser', 'type' => 0, 'is_default' => false],
        ['name' => 'core.files.download', 'description' => 'Download file', 'type' => 0, 'is_default' => false],
        ['name' => 'core.files.destroy', 'description' => 'Delete file', 'type' => 1, 'is_default' => false],
    ];

    protected $menu = [
        'name' => 'Files', 'icon' => 'folder-open', 'link' => 'core.files.index', 'order_index' => 999, 'has_children' => false,
    ];

    protected $parentMenu = '';
}
