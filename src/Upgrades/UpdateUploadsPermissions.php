<?php

namespace LaravelEnso\Files\Upgrades;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use LaravelEnso\Permissions\Models\Permission;
use LaravelEnso\Roles\Models\Role;
use LaravelEnso\Upgrade\Contracts\MigratesData;

class UpdateUploadsPermissions implements MigratesData
{
    private Collection $mapping;

    private const Mapping = [
        'core.files.uploads.store' => 'core.files.store',
        'core.files.uploads.destroy' => 'core.files.destroy',
    ];

    public function __construct()
    {
        $this->mapping = Collection::wrap(self::Mapping);
    }

    public function isMigrated(): bool
    {
        return Permission::whereIn('name', $this->mapping->keys())->exists();
    }

    public function migrateData(): void
    {
        $this->mapping->each(fn ($from, $to) => $this->update($from, $to));

        Role::where('name', '<>', Config::get('enso.config.defaultRole'))
            ->get()
            ->each
            ->writeConfig();
    }

    private function update($from, $to): void
    {
        Permission::whereName($from)->update(['migration' => $to]);
    }
}
