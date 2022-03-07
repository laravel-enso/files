<?php

namespace LaravelEnso\Files\Upgrades;

use Illuminate\Support\Facades\Schema;
use LaravelEnso\Upgrade\Contracts\MigratesTable;
use LaravelEnso\Upgrade\Helpers\Table;

class CreatedAtIndex implements MigratesTable
{
    public function isMigrated(): bool
    {
        return Table::hasIndex('files', 'files_created_at_index');
    }

    public function migrateTable(): void
    {
        Schema::table('files', fn ($table) => $table->index(['created_at']));
    }
}
