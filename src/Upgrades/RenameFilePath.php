<?php

namespace LaravelEnso\Files\Upgrades;

use Illuminate\Support\Facades\Schema;
use LaravelEnso\Upgrade\Contracts\MigratesTable;
use LaravelEnso\Upgrade\Contracts\Prioritization;
use LaravelEnso\Upgrade\Contracts\ShouldRunManually;
use LaravelEnso\Upgrade\Helpers\Table;

class RenameFilePath implements MigratesTable, Prioritization, ShouldRunManually
{
    public function priority(): int
    {
        return 107;
    }

    public function isMigrated(): bool
    {
        return Table::hasColumn('files', 'saved_name');
    }

    public function migrateTable(): void
    {
        Schema::table('files', fn ($table) => $table
            ->renameColumn('path', 'saved_name'));
    }
}
