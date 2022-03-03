<?php

namespace LaravelEnso\Files\Upgrades;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use LaravelEnso\Files\Models\File;
use LaravelEnso\Files\Models\Type;
use LaravelEnso\Upgrade\Contracts\MigratesTable;
use LaravelEnso\Upgrade\Contracts\Prioritization;
use LaravelEnso\Upgrade\Contracts\ShouldRunManually;
use LaravelEnso\Upgrade\Helpers\Table;

class DropMorphModels implements MigratesTable, Prioritization, ShouldRunManually
{
    public function priority(): int
    {
        return 110;
    }

    public function isMigrated(): bool
    {
        return $this->needUpgrade()->isEmpty();
    }

    public function migrateTable(): void
    {
        Model::unsetEventDispatcher();

        $this->needUpgrade()
            ->each(fn ($model, $morphKey) => $this->process($model, $morphKey));
    }

    private function process(string $model, string $morphKey)
    {
        $this->addForeignKey($model)
            ->migrateData($model, $morphKey);
    }

    private function addForeignKey(string $model): self
    {
        $table = $this->table($model);
        $after = $this->afterColumn($table);

        Schema::table($table, function (Blueprint $table) use ($after) {
            $table->unsignedBigInteger('file_id')->nullable()->after($after);
            $table->foreign('file_id')->references('id')->on('files')
                ->onUpdate('restrict')->onDelete('cascade');
        });

        return $this;
    }

    private function migrateData(string $model, string $morphKey)
    {
        $files = File::whereAttachableType($morphKey);
        $type = Type::for($model);
        $files->each(fn ($file) => $this->migrateFile($type, $file, $model));
    }

    private function migrateFile(Type $type, File $file, string $model)
    {
        $folder = Str::of($file->saved_name)->beforeLast('/');
        $savedName = Str::of($file->saved_name)->replace("{$folder}/", '');

        $file->update([
            'type_id' => $type->id,
            'saved_name' => $savedName,
        ]);

        $record = $model::find($file->attachable_id);

        if ($record) {
            $record->update(['file_id' => $file->id]);
        } else {
            Log::notice("File with id of {$file->id} is missing its morphed model");
        }
    }

    private function afterColumn(string $table): string
    {
        return Collection::wrap(Schema::getColumnListing($table))
            ->filter(fn ($column) => Str::endsWith($column, '_id'))
            ->last() ?? 'id';
    }

    private function needUpgrade(): Collection
    {
        $upgraded = fn ($model) => Table::hasColumn($this->table($model), 'file_id');

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
