<?php

namespace LaravelEnso\FileManager\app\Traits;

use Illuminate\Http\UploadedFile;
use LaravelEnso\FileManager\app\Models\File;
use LaravelEnso\FileManager\app\Classes\FileManager;

trait HasFile
{
    protected static function bootHasFile()
    {
        self::deleting(function ($model) {
            (new FileManager($model))
                ->delete();
        });
    }

    public function file()
    {
        return $this->morphOne(File::class, 'attachable');
    }

    public function inline()
    {
        return (new FileManager($this))
            ->inline();
    }

    public function download()
    {
        return (new FileManager($this))
            ->download();
    }

    public function temporaryLink()
    {
        return $this->file->temporaryLink();
    }

    public function upload(UploadedFile $file)
    {
        return (new FileManager($this))
            ->file($file)
            ->mimeTypes($this->mimeTypes())
            ->extensions($this->extensions())
            ->optimize($this->optimizeImages())
            ->resize($this->resizeImages())
            ->upload();
    }

    public function folder()
    {
        return property_exists($this, 'folder')
            ? $this->folder
            : 'files';
    }

    public function mimeTypes()
    {
        return property_exists($this, 'mimeTypes')
            ? $this->mimeTypes
            : [];
    }

    public function extensions()
    {
        return property_exists($this, 'extensions')
            ? $this->extensions
            : [];
    }

    public function resizeImages()
    {
        return property_exists($this, 'resizeImages')
            ? $this->resizeImages
            : [];
    }

    public function optimizeImages()
    {
        return property_exists($this, 'optimizeImages')
            ? $this->optimizeImages
            : false;
    }
}
