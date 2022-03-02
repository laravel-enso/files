<?php

use LaravelEnso\Migrator\Database\Migration;

return new class extends Migration
{
    protected array $permissions = [
        ['name' => 'administration.fileTypes.tableData', 'description' => 'Get table data for file types', 'is_default' => false],
        ['name' => 'administration.fileTypes.exportExcel', 'description' => 'Export excel for file types', 'is_default' => false],
        ['name' => 'administration.fileTypes.initTable', 'description' => 'Init table data for file types', 'is_default' => false],
        ['name' => 'administration.fileTypes.create', 'description' => 'Create tutorial', 'is_default' => false],
        ['name' => 'administration.fileTypes.edit', 'description' => 'Edit tutorial', 'is_default' => false],
        ['name' => 'administration.fileTypes.index', 'description' => 'Show file types index', 'is_default' => false],
        ['name' => 'administration.fileTypes.store', 'description' => 'Store newly created file type', 'is_default' => false],
        ['name' => 'administration.fileTypes.update', 'description' => 'Update edited file type', 'is_default' => false],
        ['name' => 'administration.fileTypes.destroy', 'description' => 'Delete file type', 'is_default' => false],
    ];

    protected array $menu = [
        'name' => 'File Types', 'icon' => 'photo-video', 'route' => 'administration.fileTypes.index', 'order_index' => 999, 'has_children' => false,
    ];

    protected ?string $parentMenu = 'Administration';
};
