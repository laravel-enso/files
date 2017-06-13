<?php

namespace LaravelEnso\FileManager\Classes;

use Illuminate\Http\UploadedFile;

class FileUploader
{
    private $files;
    private $filesPath;
    private $tempPath;

    public function __construct(string $filesPath, string $tempPath)
    {
        $this->filesPath = $filesPath;
        $this->tempPath = $tempPath;
        $this->files = collect();
    }

    public function start($payload)
    {
        return is_array($payload) ? $this->uploadFiles($payload) : $this->uploadFile($payload);
    }

    public function commit()
    {
        $this->files->each(function ($file) {
            \Storage::move(config('laravel-enso.paths.temp').'/'.$file['saved_name'],
                $this->filesPath.'/'.$file['saved_name']);
        });
    }

    public function getFiles()
    {
        return $this->files;
    }

    private function uploadFiles(array $files)
    {
        foreach ($files as $file) {
            $this->uploadFile($file);
        }
    }

    private function uploadFile(UploadedFile $file)
    {
        if (!$file->isValid()) {
            throw new \EnsoException('Error Processing File:'.$file->getClientOriginalName(), 409);
        }

        $this->uploadToTemp($file);
    }

    private function uploadToTemp($file)
    {
        $fileName = $file->getClientOriginalName();
        $savedName = $file->hashName();
        $fileSize = $file->getClientSize();
        $file->move(storage_path('app/'.$this->tempPath), $savedName);

        $this->files->push([
            'original_name' => $fileName,
            'saved_name'    => $savedName,
            'size'          => $fileSize,
        ]);
    }

    private function deleteTempFiles()
    {
        $this->files->each(function ($file) {
            \Storage::delete(config('laravel-enso.paths.temp').'/'.$file['saved_name']);
        });
    }
}
