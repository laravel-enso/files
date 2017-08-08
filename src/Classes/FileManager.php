<?php

namespace LaravelEnso\FileManager\Classes;

class FileManager
{
    private $filesPath;
    private $uploader;
    private $disk;

    public function __construct(string $filesPath, string $tempPath = null, string $disk = 'local')
    {
        $this->filesPath = $filesPath;
        $this->disk = $disk;

        $this->uploader = $tempPath
            ? new FileUploader($this->filesPath, $tempPath, $this->disk)
            : null;
    }

    public function startUpload(array $files)
    {
        $this->uploader->start($files);

        return $this;
    }

    public function commitUpload()
    {
        $this->uploader->commit();
    }

    public function getUploadedFiles()
    {
        return $this->uploader->getFiles();
    }

    public function deleteTempFiles()
    {
        $this->uploader->deleteTempFiles();
    }

    public function getInline(string $savedName)
    {
        return response()->file(
            storage_path('app'.DIRECTORY_SEPARATOR.$this->filesPath.'/'.$savedName)
        );
    }

    public function download(string $originalName, string $savedName)
    {
        return response()->download(
            storage_path('app'.DIRECTORY_SEPARATOR.$this->filesPath.'/'.$savedName),
            $originalName
        );
    }

    public function delete(string $fileName)
    {
        \Storage::disk($this->disk)
            ->delete($this->filesPath.DIRECTORY_SEPARATOR.$fileName);
    }

    public function setValidExtensions(array $extensions)
    {
        $this->uploader->setValidExtensions($extensions);
    }

    public function setValidMimeTypes(array $mimeTypes)
    {
        $this->uploader->setValidMimeTypes($mimeTypes);
    }
}
