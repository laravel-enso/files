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
        $this->uploader = $tempPath ? new FileUploader($this->filesPath, $tempPath, $this->disk) : null;
    }

    public function startUpload($payload)
    {
        $this->uploader->start($payload);

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

    public function getInline(string $originalName, string $savedName)
    {
        return response()->file(storage_path('app/'.$this->filesPath.'/'.$savedName));
    }

    public function download(string $originalName, string $savedName)
    {
        return response()->download(storage_path('app/'.$this->filesPath.'/'.$savedName), $originalName);
    }

    public function delete(string $fileName)
    {
        \Storage::disk($this->disk)->delete($this->filesPath.'/'.$fileName);
    }
}
