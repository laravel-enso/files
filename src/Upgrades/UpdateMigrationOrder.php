<?php

namespace LaravelEnso\Files\Upgrades;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use LaravelEnso\Upgrade\Contracts\BeforeMigration;
use LaravelEnso\Upgrade\Contracts\MigratesData;

class UpdateMigrationOrder implements MigratesData, BeforeMigration
{
    private const Mapping = [
        '2022_01_23_100000_create_file_types_table' => '2017_01_01_112100_create_file_types_table',
        '2018_08_25_100000_create_files_table' => '2017_01_01_112200_create_files_table',
        '2018_08_25_101000_create_structure_for_files' => '2017_01_01_112300_create_structure_for_files',
        '2018_08_25_102000_create_uploads_table' => '2017_01_01_112400_create_uploads_table',
        '2018_08_25_103000_create_structure_for_uploads' => '2017_01_01_112500_create_structure_for_uploads',
        '2022_01_17_100000_create_favorite_files_table' => '2017_01_01_112600_create_favorite_files_table',
        '2022_01_23_101000_create_structure_for_file_types' => '2017_01_01_129000_create_structure_for_file_types',
    ];

    private Collection $mapping;

    public function __construct()
    {
        $this->mapping = Collection::wrap(self::Mapping);
    }

    public function isMigrated(): bool
    {
        return DB::table('migrations')
            ->whereIn('migration', $this->mapping->keys())
            ->doesntExist();
    }

    public function migrateData(): void
    {
        $this->mapping->each(fn ($to, $from) => $this->update($from, $to));
    }

    private function update($from, $to): void
    {
        DB::table('migrations')
            ->whereMigration($from)
            ->update(['migration' => $to]);
    }
}
