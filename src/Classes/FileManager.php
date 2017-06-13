<?php

namespace LaravelEnso\FileManager\Classes;

class FileManager
{
    private $filesPath;
    private $uploader;
    private $inlineContent;
    private $downloadContent;

    public function __construct(string $filesPath, string $tempPath = null)
    {
        $this->filesPath = $filesPath;
        $this->uploader = $tempPath ? new FileUploader($this->filesPath, $tempPath) : null;
        $this->inlineContent = 'inline';
        $this->downloadContent = 'attachment';
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
        $fileResponse = $this->setFileRequest($originalName, $savedName, $this->inlineContent);

        return $fileResponse->get();
    }

    public function download(string $originalName, string $savedName)
    {
        $fileResponse = $this->setFileRequest($originalName, $savedName, $this->downloadContent);

        return $fileResponse->get();
    }

    public function delete(string $fileName)
    {
        \Storage::delete($this->filesPath.'/'.$fileName);
    }

    private function setFileRequest(string $originalName, string $savedName, string $contentDisposition)
    {
        $file = $this->getFileFromStorage($savedName);
        $mimeType = $this->getMimeType($savedName);

        return new FileResponse($file, $originalName, $contentDisposition, $mimeType);
    }

    private function getFileFromStorage(string $fileName)
    {
        return \Storage::get($this->filesPath.'/'.$fileName);
    }

    private function getMimeType(string $fileName)
    {
        return \Storage::getMimeType($this->filesPath.'/'.$fileName);
    }
}
