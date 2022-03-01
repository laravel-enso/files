<?php

namespace LaravelEnso\Files\Upgrades;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use LaravelEnso\Files\Models\File;
use LaravelEnso\Upgrade\Contracts\MigratesData;
use LaravelEnso\Upgrade\Contracts\ShouldRunManually;
use LaravelEnso\Upgrade\Helpers\Table;

class MoveFiles implements MigratesData, ShouldRunManually
{
    private array $nonStandardFolders;
    private array $renameFolders;

    public function __construct()
    {
        $this->nonStandardFolders = Config::get('enso.files.nonStandardFolders');
        $this->renameFolders = Config::get('enso.files.renameFolders');
    }

    public function isMigrated(): bool
    {
        return Table::hasColumn('files', 'saved_name')
            && $this->notMoved()->doesntExist();
    }

    public function migrateData(): void
    {
        $this->notMoved()
            ->pluck('attachable_type')
            ->unique()
            ->each(fn ($attachable) => $this->handle($attachable));

        $this->cleanup();
    }

    private function handle(string $attachable): void
    {
        $key = $this->renameFolders[$attachable] ?? $attachable;
        $folder = Str::plural($key);

        if (! Storage::has($folder)) {
            Storage::makeDirectory($folder);
        }

        File::whereAttachableType($attachable)
            ->each(fn ($file) => $this->move($file, $folder));

        $this->replace($attachable, $folder);
    }

    private function move(File $file, string $folder): void
    {
        if (Storage::exists($file->saved_name)) {
            $location = Str::replace($this->nonStandardFolders, $folder, $file->saved_name);

            if ($file->saved_name !== $location) {
                Storage::move($file->saved_name, $location);
            }
        }
    }

    private function replace(string $attachable, string $folder)
    {
        Collection::wrap($this->nonStandardFolders)
            ->each(fn ($from) => $this->notMoved()
                ->whereAttachableType($attachable)
                ->where('saved_name', 'LIKE', "{$from}/%")
                ->update(['saved_name' => DB::raw("REPLACE(saved_name, '{$from}', '{$folder}')")]));
    }

    private function notMoved(): Builder
    {
        $build = fn ($query) => Collection::wrap($this->nonStandardFolders)
            ->reduce(fn ($files, $folder) => $files
                ->orWhere('saved_name', 'LIKE', "{$folder}/%"), $query);

        return File::where($build);
    }

    private function cleanup(): void
    {
        Collection::wrap($this->nonStandardFolders)
            ->filter(fn ($folder) => count(Storage::files($folder)) === 0)
            ->each(fn ($folder) => Storage::deleteDirectory($folder));
    }
}
