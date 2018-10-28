<?php

use LaravelEnso\StructureManager\app\Classes\StructureMigration;

class CreateStructureForUploads extends StructureMigration
{
    protected $permissions = [
        ['name' => 'core.uploads.store', 'description' => 'Upload file', 'type' => 1, 'is_default' => true],
        ['name' => 'core.uploads.destroy', 'description' => 'Delete upload', 'type' => 1, 'is_default' => true],
    ];
}
