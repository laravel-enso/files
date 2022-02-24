<?php

namespace LaravelEnso\Files\Upgrades;

use Illuminate\Support\Facades\Schema;
use LaravelEnso\Upgrade\Contracts\BeforeMigration;
use LaravelEnso\Upgrade\Contracts\MigratesTable;
use LaravelEnso\Upgrade\Helpers\Column;

class FileIds implements MigratesTable, BeforeMigration
{
    public function isMigrated(): bool
    {
        return Column::isBigInteger('files', 'id');
    }

    public function migrateTable(): void
    {
        Schema::table('files', fn ($table) => $table->id()->change());
    }
}
