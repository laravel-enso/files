<?php

namespace LaravelEnso\FileManager\app\Traits;

use LaravelEnso\FileManager\app\Models\File;
use LaravelEnso\FileManager\app\Classes\FileManager;
use Symfony\Component\HttpFoundation\File\File as SymfonyFile;

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

    /**
     * @param \Illuminate\Http\UploadedFile|\Illuminate\Http\File $file
     */
    public function upload(SymfonyFile $file)
    {
        if (! $file instanceof \Illuminate\Http\File && ! $file instanceof \Illuminate\Http\UploadedFile) {
            throw new \InvalidArgumentException('$file must be a File or UploadedFile object.');
        }

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
