<?php

namespace LaravelEnso\Files\Traits;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use LaravelEnso\Files\Models\File;

trait HasFile
{
    public function file(): Relation
    {
        return $this->morphOne(File::class, 'attachable')
            ->withDefault();
    }

    public function folder(): string
    {
        if (App::environment('testing')) {
            $directory = Config::get('enso.files.testingFolder');

            if (! Storage::has($directory)) {
                Storage::makeDirectory($directory);
            }

            return $directory;
        }

        return $this->folder;
    }

    public function mimeTypes(): array
    {
        return property_exists($this, 'mimeTypes')
            ? $this->mimeTypes
            : [];
    }

    public function extensions(): array
    {
        return property_exists($this, 'extensions')
            ? $this->extensions
            : [];
    }

    public function resizeImages(): array
    {
        return property_exists($this, 'resizeImages')
            ? $this->resizeImages
            : [];
    }

    public function optimizeImages(): bool
    {
        return property_exists($this, 'optimizeImages')
            ? $this->optimizeImages
            : false;
    }

    protected static function bootHasFile()
    {
        self::deleting(function ($model) {
            if ($model->file->exists) {
                $model->file->delete();
            }
        });
    }
}
