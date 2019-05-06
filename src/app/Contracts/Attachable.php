<?php

namespace LaravelEnso\FileManager\app\Contracts;

use Symfony\Component\HttpFoundation\File\File;

interface Attachable
{
    public function file();

    public function inline();

    public function download();

    public function temporaryLink();

    /**
     * @param \Illuminate\Http\UploadedFile|\Illuminate\Http\File $file
     *
     * @return mixed
     */
    public function upload(File $file);

    public function folder();

    public function mimeTypes();

    public function extensions();

    public function resizeImages();

    public function optimizeImages();
}
