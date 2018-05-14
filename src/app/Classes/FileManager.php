<?php

namespace LaravelEnso\FileManager\app\Classes;

use LaravelEnso\FileManager\app\Exceptions\FileUploadException;

class FileManager
{
    private $path;
    private $tempPath;
    private $uploader;
    private $disk = 'local';

    public function __construct(string $path)
    {
        $this->path = $path;
    }

    public function startUpload(array $files)
    {
        $this->setUploader();

        $this->uploader->start($files);

        return $this;
    }

    public function commitUpload()
    {
        $this->uploader->commit();

        return $this;
    }

    public function uploadedFiles()
    {
        return $this->uploader->files();
    }

    public function deleteTempFiles()
    {
        $this->uploader->deleteTempFiles();
    }

    public function inline(string $savedName)
    {
        return response()->file(
            storage_path('app/'.$this->path.'/'.$savedName)
        );
    }

    public function download(string $originalName, string $savedName)
    {
        return \Storage::download(
            $this->path.DIRECTORY_SEPARATOR.$savedName,
            $originalName
        );
    }

    public function delete(string $fileName)
    {
        \Storage::disk($this->disk)
            ->delete($this->path.DIRECTORY_SEPARATOR.$fileName);
    }

    public function tempPath(string $path)
    {
        $this->tempPath = $path;

        return $this;
    }

    public function disk(string $disk)
    {
        $this->disk = $disk;

        return $this;
    }

    public function validExtensions(array $extensions)
    {
        $this->setUploader();

        $this->uploader->setValidExtensions($extensions);

        return $this;
    }

    public function validMimeTypes(array $mimeTypes)
    {
        $this->setUploader();

        $this->uploader->setValidMimeTypes($mimeTypes);

        return $this;
    }

    private function setUploader()
    {
        if (isset($this->uploader)) {
            return;
        }

        if (!isset($this->tempPath)) {
            throw new FileUploadException(__(
                'You must set a temporary path before uploading a file'
            ));
        }

        $this->uploader = new Uploader(
            $this->path,
            $this->tempPath,
            $this->disk
        );
    }
}
