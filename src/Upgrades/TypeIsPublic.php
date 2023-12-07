<?php

namespace LaravelEnso\Files\Upgrades;

use Illuminate\Support\Facades\Schema;
use LaravelEnso\Files\Contracts\PublicFile;
use LaravelEnso\Files\Models\Type;
use LaravelEnso\Upgrade\Contracts\MigratesData;
use LaravelEnso\Upgrade\Contracts\MigratesTable;
use LaravelEnso\Upgrade\Helpers\Table;

class TypeIsPublic implements MigratesTable, MigratesData
{
    public function isMigrated(): bool
    {
        return ! Table::hasColumn('file_types', 'is_public');
    }

    public function migrateTable(): void
    {
        Schema::table('file_types', fn ($table) => $table->boolean('is_public')
            ->default(false)
            ->after('description'));

        Schema::table('file_types', fn ($table) => $table->boolean('is_public')
            ->default(null)
            ->change());
    }

    public function migrateData(): void
    {
        Type::whereNotNull('model')->get()
            ->each(fn ($type) => $type->update([
                'is_public' => $type->model() instanceof PublicFile,
            ]));
    }
}
