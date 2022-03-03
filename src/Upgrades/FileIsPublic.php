<?php

namespace LaravelEnso\Files\Upgrades;

use Illuminate\Support\Facades\Schema;
use LaravelEnso\Files\Models\File;
use LaravelEnso\Upgrade\Contracts\MigratesData;
use LaravelEnso\Upgrade\Contracts\MigratesTable;
use LaravelEnso\Upgrade\Contracts\ShouldRunManually;
use LaravelEnso\Upgrade\Helpers\Table;

class FileIsPublic implements MigratesTable, MigratesData, ShouldRunManually
{
    public function isMigrated(): bool
    {
        return Table::hasColumn('files', 'is_public');
    }

    public function migrateTable(): void
    {
        Schema::table('files', fn ($table) => $table
            ->boolean('is_public')->nullable()->after('mime_type'));
    }

    public function migrateData(): void
    {
        File::whereNull('is_public')->update(['is_public' => false]);
    }
}
