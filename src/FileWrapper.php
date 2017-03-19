<?php

namespace LaravelEnso\FileManager;

use LaravelEnso\Helpers\Classes\AbstractObject;

class FileWrapper extends AbstractObject
{
    public $file;
    public $originalName;
    public $statusCode;
    public $mimeType;

    public function __construct($file, String $mimeType)
    {
        $this->file = $file;
        $this->statusCode = 200;
        $this->mimeType = $mimeType;
    }

    public function getDownloadResponse()
    {
        return $this->getResponse('attachment');
    }

    public function getInlineResponse()
    {
        return $this->getResponse('inline');
    }

    private function getResponse(String $contentDisposition)
    {
        return response()->make($this->file, $this->statusCode, [

            'Content-Type'        => $this->mimeType,
            'Content-Disposition' => $contentDisposition.'; filename="'.$this->originalName.'"',
        ]);
    }
}
