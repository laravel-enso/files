<?php

namespace LaravelEnso\Files\Upgrades;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use LaravelEnso\Files\Models\File;
use LaravelEnso\Upgrade\Contracts\MigratesData;

class MoveFiles implements MigratesData
{
    private const ToMove = ['files', 'imports', 'pictures', 'carousel', 'wiki_logos', 'howToVideos'];

    public function isMigrated(): bool
    {
        return $this->notMoved()->doesntExist();
    }

    public function migrateData(): void
    {
        $this->notMoved()
            ->pluck('attachable_type')
            ->each(fn ($attachable) => $this->handle($attachable));
    }

    private function handle(string $attachable): void
    {
        $folder = Str::plural($attachable);

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
            $location = Str::replace(self::ToMove, $folder, $file->saved_name);
            Storage::move($file->saved_name, $location);
        }
    }

    private function replace(string $attachable, string $folder)
    {
        Collection::wrap(self::ToMove)
            ->each(fn ($from) => $this->notMoved()
                ->whereAttachableType($attachable)
                ->where('saved_name', 'LIKE', "{$from}/%")
                ->update(['saved_name' => DB::raw("REPLACE(saved_name, '{$from}', '{$folder}')")]));
    }

    private function notMoved(): Builder
    {
        return Collection::wrap(self::ToMove)
            ->reduce(fn ($files, $folder) => $files
                ->orWhere('saved_name', 'LIKE', "{$folder}/%"), File::query());
    }
}
