<?php

namespace LaravelEnso\Files\app\Services;

use Illuminate\Support\Facades\DB;
use LaravelEnso\Files\app\Models\File;
use LaravelEnso\Files\app\Models\Upload;
use LaravelEnso\Files\app\Exceptions\FileExistsException;
use LaravelEnso\Files\app\Http\Resources\File as Resource;

class UploadManager
{
    private $uploadedFiles;
    private $upload;
    private $files;

    public function __construct(Upload $upload, array $files)
    {
        $this->uploadedFiles = collect();
        $this->upload = $upload;
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
        $existing = collect($this->files)->map(function ($file) {
            return $file->getClientOriginalName();
        })->intersect($this->existingFiles());

        if ($existing->isNotEmpty()) {
            throw new FileExistsException(__(
                'File(s): :files already uploaded',
                ['files' => $existing->implode(', ')]
            ));
        }

        return $this;
    }

    private function uploadFiles()
    {
        collect($this->files)->each(function ($file) {
            $upload = tap(Upload::create())
                ->upload($file);

            $this->uploadedFiles->push(new Resource(
                $upload->file->load(['attachable', 'createdBy']))
            );
        });
    }

    private function existingFiles()
    {
        return File::forUser(auth()->user())
            ->whereAttachableType(Upload::class)
            ->pluck('original_name');
    }
}
