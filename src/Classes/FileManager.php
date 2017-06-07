<?php

namespace LaravelEnso\FileManager\Classes;

use Carbon\Carbon;

class FileManager
{
    public $uploadedFiles;
    private $path;
    private $status = null;

    public function __construct($path)
    {
        $this->uploadedFiles = collect();
        $this->path = $path;
        $this->status = new FileManagerStatus();
    }

    /** Starts the 2 step upload process for a list of files
     * @param $request - array of files
     */
    public function startUpload($request)
    {
        foreach ($request as $file) {
            if (!$file->isValid()) {
                $this->logError($file);
            }

            $this->uploadToTemp($file);
        }

        $this->setStatus(__('Upload'));
    }

    /** Starts the upload process for a single file. Method might be called multiple times if needed, followed by
     * by a single commitUpload call, that will complete the upload for all given files.
     *
     * @param $file
     */
    public function startSingleFileUpload($file)
    {
        if (!$file->isValid()) {
            $this->logError($file);
        }

        $this->uploadToTemp($file);
        $this->setStatus(__('Upload'));
    }

    public function commitUpload()
    {
        $this->uploadedFiles->each(function ($uploadedFile) {
            \Storage::move(config('laravel-enso.paths.temp').'/'.$uploadedFile['saved_name'],
                $this->path.'/'.$uploadedFile['saved_name']);
        });

        return $this->status;
    }

    public function delete(String $fileName)
    {
        \Storage::delete($this->path.'/'.$fileName);
        $this->setStatus(__('Delete'));

        return $this->status;
    }

    /** Load file from disk and give it back within a wrapper containing also mimeType
     * @param string $fileSavedName
     *
     * @return FileWrapper
     */
    public function getFile(String $fileSavedName)
    {
        $file = \Storage::get($this->path.'/'.$fileSavedName);
        $mimeType = \Storage::getMimeType($this->path.'/'.$fileSavedName);

        return new FileWrapper($file, $mimeType);
    }

    public function getStatus()
    {
        return $this->status;
    }

    /************* private functions **************/

    private function setStatus(String $operation)
    {
        $this->status->level = $this->status->errors->count() ? 'error' : 'success';
        $this->status->message = $this->status->errors->count() ? $operation.' encountered '.$this->status->errors->count().' errors'
            : $operation.' was successfull';
    }

    private function logError($file)
    {
        $this->status->errors->push([

            'error' => __('File is not valid'),
            'file'  => $file,
        ]);
    }

    //TODO on uploadCommit, we should probably cleanup the object for reuse
    private function cleanupOnUploadCommit()
    {
        $this->status = null;
        $this->uploadedFiles = collect();
    }

    private function uploadToTemp($file)
    {
        $fileName = $file->getClientOriginalName();
        $randomPrefix = mt_rand(100, 1000);
        $fileSavedName = md5($randomPrefix.$fileName.Carbon::now()).'.'.$file->getClientOriginalExtension();
        $fileSize = $file->getClientSize();
        $file->move(storage_path('app/'.config('laravel-enso.paths.temp')), $fileSavedName);

        $this->uploadedFiles->push([
            'original_name' => $fileName,
            'saved_name'    => $fileSavedName,
            'size'          => $fileSize,
        ]);
    }
}
