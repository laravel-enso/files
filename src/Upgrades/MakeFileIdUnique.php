<?php

namespace LaravelEnso\Files\Upgrades;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use LaravelEnso\Upgrade\Contracts\MigratesTable;
use LaravelEnso\Upgrade\Contracts\Prioritization;
use LaravelEnso\Upgrade\Contracts\ShouldRunManually;
use LaravelEnso\Upgrade\Helpers\Table;

class MakeFileIdUnique implements MigratesTable, Prioritization, ShouldRunManually
{
    public function priority(): int
    {
        return 150;
    }

    public function isMigrated(): bool
    {
        return $this->needUpgrade()->isEmpty();
    }

    public function migrateTable(): void
    {
        Model::unsetEventDispatcher();

        $this->needUpgrade()->each(fn ($model) => $this->upgrade($model));
    }

    private function upgrade(string $model): void
    {
        $table = $this->table($model);

        Schema::table($table, fn ($table) => $table->unique('file_id'));
    }

    private function needUpgrade(): Collection
    {
        $upgraded = fn ($model) => Table::hasIndex(
            $this->table($model),
            "{$this->table($model)}_file_id_unique"
        );

        return $this->models()->reject($upgraded);
    }

    private function table(string $model): string
    {
        return (new $model())->getTable();
    }

    private function models(): Collection
    {
        $upgrade = Config::get('enso.files.upgrade');

        return Collection::wrap($upgrade)
            ->filter(fn ($model) => class_exists($model));
    }
}
