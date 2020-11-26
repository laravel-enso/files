<?php

namespace LaravelEnso\Files\Upgrades;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use LaravelEnso\Files\Facades\FileBrowser;
use LaravelEnso\Files\Models\File;
use LaravelEnso\Upgrade\Contracts\MigratesData;
use LaravelEnso\Upgrade\Contracts\MigratesPostDataMigration;
use LaravelEnso\Upgrade\Contracts\MigratesTable;
use LaravelEnso\Upgrade\Helpers\Table;

class FileDiskPath implements MigratesTable, MigratesData, MigratesPostDataMigration
{
    public function isMigrated(): bool
    {
        return Table::hasColumn('files', 'is_active');
    }

    public function migrateTable(): void
    {
        Schema::table('files', function (Blueprint $table) {
            $table->string('disk')->after('original_name')->nullable();
            $table->string('path')->after('disk')->nullable();
        });
    }

    public function migrateData(): void
    {
        File::whereNull('disk')->update([
            'disk' => Config::get('filesystems.default'),
        ]);

        $types = File::select('attachable_type')
            ->distinct('attachable_type')
            ->pluck('attachable_type');

        //LaravelEnso\Files\Models\File::whereAttachableType('document')->doesntHave('attachable')->count()

        $types->each(function (string $type) {
            $folder = FileBrowser::folder($type);
            $separator = DIRECTORY_SEPARATOR;

            File::whereAttachableType($type)
                ->update([
                    'path' => DB::raw("CONCAT('{$folder}','{$separator}', saved_name)"),
                ]);
        });
    }

    public function migratePostDataMigration(): void
    {
        Schema::table('files', function (Blueprint $table) {
            $table->string('disk')->nullable(false)->change();
            $table->string('path')->nullable(false)->change();
            $table->dropColumn(['saved_name']);
        });
    }
}
