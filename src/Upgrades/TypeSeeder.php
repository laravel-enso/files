<?php

namespace LaravelEnso\Files\Upgrades;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use LaravelEnso\Documents\Models\Document;
use LaravelEnso\Files\Database\Seeders\TypeSeeder as Seeder;
use LaravelEnso\Files\Models\File;
use LaravelEnso\Files\Models\Type;
use LaravelEnso\Upgrade\Contracts\MigratesData;
use LaravelEnso\Upgrade\Contracts\MigratesPostDataMigration;

class TypeSeeder implements MigratesData, MigratesPostDataMigration
{
    public function isMigrated(): bool
    {
        return Schema::hasTable('file_types') && Type::query()->exists();
    }

    public function migrateData(): void
    {
        $seeder = Seeder::class;

        Artisan::command("db:seed --class={$seeder}", ['--force' => true]);
    }

    public function migratePostDataMigration(): void
    {
        if (! Storage::has('uploads')) {
            Storage::makeDirectory('uploads');
        }

        $this->replace('uploads');

        if (class_exists(Document::class)) {
            if (! Storage::has('documents')) {
                Storage::makeDirectory('documents');
            }

            $this->replace('documents');
        }
    }

    private function replace(string $attachable)
    {
        File::whereAttachableType($attachable)
            ->where('path', 'LIKE', 'files/%')
            ->update(['path' => DB::raw("REPLACE(path, 'files',  $attachable)")]);
    }
}
