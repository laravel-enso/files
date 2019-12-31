<?php

namespace LaravelEnso\Files\App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use LaravelEnso\Files\App\Exceptions\File as FileException;
use LaravelEnso\Files\App\Http\Resources\File as Resource;
use LaravelEnso\Files\App\Models\File;
use LaravelEnso\Files\App\Models\Upload;

class UploadManager
{
    private Collection $uploadedFiles;
    private Collection $files;

    public function __construct(array $files)
    {
        $this->uploadedFiles = new Collection();
        $this->files = new Collection($files);
    }

    public function handle(): Collection
    {
        DB::transaction(function () {
            $this->checkExisting()
                ->uploadFiles();
        });

        return $this->uploadedFiles;
    }

    private function checkExisting(): self
    {
        $existing = $this->files
            ->map(fn ($file) => $file->getClientOriginalName())
            ->intersect($this->existingFiles());

        if ($existing->isNotEmpty()) {
            throw FileException::duplicates($existing->implode(', '));
        }

        return $this;
    }

    private function uploadFiles(): void
    {
        $this->files->each(fn ($file) => $this->uploadFile($file));
    }

    private function uploadFile($file): void
    {
        $upload = tap(Upload::create())
            ->upload($file);

        $this->uploadedFiles->push(new Resource(
            $upload->file->load(['attachable', 'createdBy.avatar']))
        );
    }

    private function existingFiles(): Collection
    {
        return File::forUser(Auth::user())
            ->whereAttachableType(Upload::class)
            ->pluck('original_name');
    }
}
