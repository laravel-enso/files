<?php

namespace LaravelEnso\Files\Traits;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use LaravelEnso\Core\Models\User;
use LaravelEnso\Files\Models\File;
use Symfony\Component\HttpFoundation\StreamedResponse;

trait HasFile
{
    public function file(): Relation
    {
        return $this->morphOne(File::class, 'attachable')
            ->withDefault();
    }

    public function inline(): StreamedResponse
    {
        return $this->file->inline();
    }

    public function download(): StreamedResponse
    {
        return $this->file->download();
    }

    public function temporaryLink(): string
    {
        return $this->file->temporaryLink();
    }

    public function attach(string $path, string $originalName, ?User $user = null): void
    {
        $this->file->attach($path, $originalName, $user);
    }

    public function upload(UploadedFile $file): void
    {
        $this->file->upload($this, $file);
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

    public function storagePath(): ?string
    {
        return $this->file
            ? $this->file->path
            : null;
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
        self::deleting(fn ($model) => $model->file->delete());
    }
}
