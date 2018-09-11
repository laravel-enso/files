<?php

namespace LaravelEnso\FileManager\app\Contracts;

use Illuminate\Http\UploadedFile;

interface Attachable
{
    public function file();

    public function inline();

    public function download();

    public function temporaryLink();

    public function upload(UploadedFile $file);

    public function folder();

    public function mimeTypes();

    public function extensions();

    public function resizeImages();

    public function optimizeImages();
}
