<?php

namespace LaravelEnso\Files\Upgrades;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
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
        Schema::table('files', fn ($table) => $table
            ->string('path')->after('original_name')->nullable());
    }

    public function migrateData(): void
    {
        File::distinct('attachable_type')
            ->pluck('attachable_type')
            ->each(fn (string $type) => File::whereAttachableType($type)->update([
                'path' => DB::raw("CONCAT('{$this->folder($type)}', '/', saved_name)"),
            ]));
    }

    public function migratePostDataMigration(): void
    {
        Schema::table('files', function (Blueprint $table) {
            $table->string('path')->nullable(false)->change();
            $table->dropColumn('saved_name');
        });
    }

    private function folder(string $type)
    {
        $model = Relation::getMorphedModel($type);

        return (new $model())->folder();
    }
}
