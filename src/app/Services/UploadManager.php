<?php

namespace LaravelEnso\Files\app\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use LaravelEnso\Files\app\Exceptions\File as FileException;
use LaravelEnso\Files\app\Http\Resources\File as Resource;
use LaravelEnso\Files\app\Models\File;
use LaravelEnso\Files\app\Models\Upload;

class UploadManager
{
    private $uploadedFiles;
    private $files;

    public function __construct(array $files)
    {
        $this->uploadedFiles = collect();
        $this->files = $files;
    }

    public function handle()
    {
        DB::transaction(function () {
            $this->checkExisting()
                ->uploadFiles();
        });

        return $this->uploadedFiles;
    }

    private function checkExisting()
    {
        $existing = collect($this->files)
            ->map(fn($file) => $file->getClientOriginalName())
            ->intersect($this->existingFiles());

        if ($existing->isNotEmpty()) {
            throw FileException::duplicates($existing->implode(', '));
        }

        return $this;
    }

    private function uploadFiles()
    {
        collect($this->files)->each(function ($file) {
            $upload = tap(Upload::create())
                ->upload($file);

            $this->uploadedFiles->push(new Resource(
                $upload->file->load(['attachable', 'createdBy.avatar']))
            );
        });
    }

    private function existingFiles()
    {
        return File::forUser(Auth::user())
            ->whereAttachableType(Upload::class)
            ->pluck('original_name');
    }
}
