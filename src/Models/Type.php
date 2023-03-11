<?php

namespace LaravelEnso\Files\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File as FileFacade;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use LaravelEnso\Files\Contracts\Attachable;
use LaravelEnso\Files\Contracts\PublicFile;
use LaravelEnso\Rememberable\Traits\Rememberable;
use LaravelEnso\Tables\Traits\TableCache;

class Type extends Model
{
    use HasFactory, Rememberable, TableCache;

    protected $table = 'file_types';

    protected $guarded = [];

    protected $casts = ['is_browsable' => 'boolean', 'is_system' => 'boolean'];

    protected array $rememberableKeys = ['id', 'model'];

    public function files()
    {
        return $this->hasMany(File::class);
    }

    public function scopeBrowsable(Builder $query): Builder
    {
        return $query->whereIsBrowsable(true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderByDesc('is_system')->orderBy('id');
    }

    public function icon(): string | array
    {
        return Str::contains($this->icon, ' ')
            ? explode(' ', $this->icon)
            : $this->icon;
    }

    public function model(): Attachable
    {
        return new $this->model;
    }

    public function isPublic(): bool
    {
        return $this->model() instanceof PublicFile;
    }

    public static function for(string $model): self
    {
        return self::cacheGetBy('model', $model)
            ?? self::factory()->model($model)->create();
    }

    public function folder(): string
    {
        $folder = App::runningUnitTests()
            ? Config::get('enso.files.testingFolder')
            : $this->folder;

        if (!Storage::has($folder)) {
            Storage::makeDirectory($folder);
        }

        return $folder;
    }

    public function path(string $filename): string
    {
        return "{$this->folder()}/{$filename}";
    }

    public function move(): void
    {
        $from = Storage::path($this->getOriginal('folder'));
        $to = Storage::path($this->folder);

        if (FileFacade::isDirectory($to)) {
            FileFacade::copyDirectory($from, $to);
            FileFacade::deleteDirectory($from);
        } else {
            FileFacade::moveDirectory($from, $to);
        }
    }
}
