<?php

use LaravelEnso\Migrator\Database\Migration;

return new class extends Migration {
    protected array $permissions = [
        ['name' => 'core.files.uploads.store', 'description' => 'Upload file', 'is_default' => true],
        ['name' => 'core.files.uploads.destroy', 'description' => 'Delete upload', 'is_default' => true],
    ];
};
