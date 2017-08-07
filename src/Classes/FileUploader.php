<?php

namespace LaravelEnso\FileManager\Classes;

use Illuminate\Http\UploadedFile;

class FileUploader
{
    private $files;
    private $filesPath;
    private $tempPath;
    private $disk;
    private $validExtensions;
    private $validMymeTypes;

    public function __construct(string $filesPath, string $tempPath, string $disk)
    {
        $this->filesPath = $filesPath;
        $this->tempPath = $tempPath;
        $this->disk = $disk;
        $this->files = collect();
        $this->validExtensions = [];
        $this->validMimeTypes = [];
    }

    public function start(array $files)
    {
        foreach ($files as $file) {
            $this->upload($file);
        }
    }

    public function commit()
    {
        $this->files->each(function ($file) {
            \Storage::disk($this->disk)->move(
                config('laravel-enso.paths.temp').DIRECTORY_SEPARATOR.$file['saved_name'],
                $this->filesPath.DIRECTORY_SEPARATOR.$file['saved_name']
            );
        });
    }

    public function getFiles()
    {
        return $this->files;
    }

    private function upload(UploadedFile $file)
    {
        $this->validateFile($file);
        $this->validateExtension($file);
        $this->validateMimeType($file);
        $this->uploadToTemp($file);
    }

    private function uploadToTemp(UploadedFile $file)
    {
        $fileName = $file->getClientOriginalName();
        $savedName = $file->hashName();
        $tempPath = storage_path('app'.DIRECTORY_SEPARATOR.$this->tempPath);
        $file->move($tempPath, $savedName);
        $fileSize = \File::size($tempPath.DIRECTORY_SEPARATOR.$savedName);

        $this->files->push([
            'original_name' => $fileName,
            'saved_name'    => $savedName,
            'size'          => $fileSize,
            'full_path'     => $tempPath.DIRECTORY_SEPARATOR.$savedName,
        ]);
    }

    public function deleteTempFiles()
    {
        $this->files->each(function ($file) {
            $fileWithPath =
                config('laravel-enso.paths.temp').DIRECTORY_SEPARATOR.$file['saved_name'];

            return \Storage::has($fileWithPath)
                ? \Storage::disk($this->disk)->delete($fileWithPath)
                : null;
        });
    }

    public function setValidExtensions(array $extensions)
    {
        $this->validExtensions = $extensions;
    }

    public function setValidMimeTypes(array $mimeTypes)
    {
        $this->validMimeTypes = $mimeTypes;
    }

    private function validateFile(UploadedFile $file)
    {
        if ($file->isValid()) {
            return true;
        }

        $this->deleteTempFiles();

        throw new \EnsoException(
            'Error Processing File:'.$file->getClientOriginalName(), 'error', [], 409
        );
    }

    private function validateExtension(UploadedFile $file)
    {
        if (empty($this->validExtensions) || $this->extensionIsValid($file)) {
            return true;
        }

        $this->deleteTempFiles();

        throw new \EnsoException(
            __('Allowed extensions').': '.implode(', ', $this->validExtensions), 'error', [], 409
        );
    }

    private function extensionIsValid(UploadedFile $file)
    {
        return in_array($file->getClientOriginalExtension(), $this->validExtensions);
    }

    private function validateMimeType(UploadedFile $file)
    {
        if (empty($this->validMimeTypes) || $this->mimeTypeIsValid($file)) {
            return true;
        }

        $this->deleteTempFiles();

        throw new \EnsoException(
            __('Allowed mime types').': '.implode(', ', $this->validMimeTypes), 'error', [], 409
        );
    }

    private function mimeTypeIsValid(UploadedFile $file)
    {
        return in_array($file->getClientMimeType(), $this->validMimeTypes);
    }
}
