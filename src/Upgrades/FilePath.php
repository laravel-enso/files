<?php

namespace LaravelEnso\Files\Upgrades;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use LaravelEnso\Files\Facades\FileBrowser;
use LaravelEnso\Files\Models\File;
use LaravelEnso\Upgrade\Contracts\MigratesData;
use LaravelEnso\Upgrade\Contracts\MigratesPostDataMigration;
use LaravelEnso\Upgrade\Contracts\MigratesTable;
use LaravelEnso\Upgrade\Helpers\Table;

class FilePath implements MigratesTable, MigratesData, MigratesPostDataMigration
{
    public function isMigrated(): bool
    {
        return Table::hasColumn('files', 'path');
    }

    public function migrateTable(): void
    {
        Schema::table('files', function (Blueprint $table) {
            $table->string('path')->after('original_name')->nullable();
        });
    }

    public function migrateData(): void
    {
        $types = File::select('attachable_type')
            ->distinct('attachable_type')
            ->pluck('attachable_type');

        $types->each(function (string $type) {
            $folder = FileBrowser::folder($type);

            File::whereAttachableType($type)->update([
                'path' => DB::raw("CONCAT('{$folder}/', saved_name)"),
            ]);
        });
    }

    public function migratePostDataMigration(): void
    {
        Schema::table('files', function (Blueprint $table) {
            $table->string('path')->nullable(false)->change();
            $table->dropColumn(['saved_name']);
        });
    }
}
