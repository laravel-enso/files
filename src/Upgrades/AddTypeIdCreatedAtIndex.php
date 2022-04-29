<?php

namespace LaravelEnso\Files\Upgrades;

use Illuminate\Support\Facades\Schema;
use LaravelEnso\Upgrade\Contracts\MigratesTable;
use LaravelEnso\Upgrade\Contracts\Prioritization;
use LaravelEnso\Upgrade\Contracts\ShouldRunManually;
use LaravelEnso\Upgrade\Helpers\Table;

class AddTypeIdCreatedAtIndex implements MigratesTable, Prioritization, ShouldRunManually
{
    private const Index = 'files_type_id_created_at_index';

    public function priority(): int
    {
        return 106;
    }

    public function isMigrated(): bool
    {
        return Table::hasIndex('files', self::Index);
    }

    public function migrateTable(): void
    {
        Schema::table('files', fn ($table) => $table
            ->index(['type_id', 'created_at']));
    }
}
