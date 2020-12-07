<?php

namespace LaravelEnso\Files\Upgrades;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use LaravelEnso\DataImport\Models\RejectedImport;
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

        RejectedImport::all()->each(fn ($rejected) => $this->handle($rejected));
    }

    public function migratePostDataMigration(): void
    {
        Schema::table('files', function (Blueprint $table) {
            $table->string('path')->nullable(false)->change();
            $table->dropColumn('saved_name');
        });
    }

    private function handle(RejectedImport $rejected): void
    {
        $folder = "{$rejected->folder()}/rejected_{$rejected->id}";

        $path = Collection::wrap(Storage::files($folder))
            ->first(fn ($file) => Str::of($file)->endsWith('.xlsx'));

        $xlsx = Str::of($path)->explode('/')->last();

        Storage::move($path, "imports/{$xlsx}");

        Storage::deleteDirectory($folder);
    }

    private function folder(string $type): string
    {
        $model = Relation::getMorphedModel($type);

        return (new $model())->folder();
    }
}
