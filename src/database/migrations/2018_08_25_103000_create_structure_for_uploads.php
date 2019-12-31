<?php

use LaravelEnso\Migrator\App\Database\Migration;
use LaravelEnso\Permissions\App\Enums\Types;

class CreateStructureForUploads extends Migration
{
    protected $permissions = [
        ['name' => 'core.uploads.store', 'description' => 'Upload file', 'type' => Types::Write, 'is_default' => true],
        ['name' => 'core.uploads.destroy', 'description' => 'Delete upload', 'type' => Types::Write, 'is_default' => true],
    ];
}
