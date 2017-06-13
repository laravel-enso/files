<?php

namespace LaravelEnso\FileManager\Classes;

class FileResponse
{
    private $originalName;
    private $file;
    private $mimeType;
    private $contentDisposition;
    private $statusCode;

    public function __construct(string $file, string $originalName, string $contentDisposition, string $mimeType)
    {
        $this->file = $file;
        $this->originalName = $originalName;
        $this->contentDisposition = $contentDisposition;
        $this->mimeType = $mimeType;
        $this->statusCode = 200;
    }

    public function get()
    {
        return response()->make($this->file, $this->statusCode, [
            'Content-Type'        => $this->mimeType,
            'Content-Disposition' => $this->contentDisposition.'; filename="'.$this->originalName.'"',
        ]);
    }
}