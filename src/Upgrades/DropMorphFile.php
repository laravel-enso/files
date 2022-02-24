<?php

namespace LaravelEnso\Files\Upgrades;

use Illuminate\Support\Facades\Schema;
use LaravelEnso\Upgrade\Contracts\MigratesTable;
use LaravelEnso\Upgrade\Contracts\Prioritization;
use LaravelEnso\Upgrade\Contracts\ShouldRunManually;
use LaravelEnso\Upgrade\Helpers\Column;

class DropMorphFile implements MigratesTable, Prioritization, ShouldRunManually
{
    public function priority(): int
    {
        return 100;
    }

    public function isMigrated(): bool
    {
        return Column::isNullable('files', 'attachable_type');
    }

    public function migrateTable(): void
    {
        Schema::table('files', function ($table) {
            $table->string('attachable_type')->nullable()->change();
            $table->unsignedBigInteger('attachable_id')->nullable()->change();
        });
    }
}
