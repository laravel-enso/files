<?php

namespace LaravelEnso\FileManager\Classes;

class FileManager
{
    private $filesPath;
    private $uploader;
    private $disk;
    private const InlineContent = 'inline';
    private const DownloadContent = 'attachment';

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
        $fileResponse = $this->makeFileResponse($originalName, $savedName, self::InlineContent);

        return $fileResponse->get();
    }

    public function download(string $originalName, string $savedName)
    {
        $fileResponse = $this->makeFileResponse($originalName, $savedName, self::DownloadContent);

        return $fileResponse->get();
    }

    public function delete(string $fileName)
    {
        \Storage::disk($this->disk)->delete($this->filesPath.'/'.$fileName);
    }

    private function makeFileResponse(string $originalName, string $savedName, string $contentDisposition)
    {
        $file = $this->getFileFromStorage($savedName);
        $mimeType = $this->getMimeType($savedName);

        return new FileResponse($file, $originalName, $contentDisposition, $mimeType);
    }

    private function getFileFromStorage(string $fileName)
    {
        return \Storage::disk($this->disk)->get($this->filesPath.'/'.$fileName);
    }

    private function getMimeType(string $fileName)
    {
        return \Storage::disk($this->disk)->getMimeType($this->filesPath.'/'.$fileName);
    }
}
