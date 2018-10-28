<?php

namespace LaravelEnso\FileManager\app\Models;

use Illuminate\Database\Eloquent\Model;
use LaravelEnso\FileManager\app\Traits\HasFile;
use LaravelEnso\FileManager\app\Contracts\Attachable;
use LaravelEnso\FileManager\app\Contracts\VisibleFile;
use LaravelEnso\FileManager\app\Exceptions\UploadException;
use LaravelEnso\FileManager\app\Http\Resources\File as Resource;

class Upload extends Model implements Attachable, VisibleFile
{
    use HasFile;

    protected $optimizeImages = true;

    public function isDeletable()
    {
        return request()->user()
            ->can('handle', $this->file);
    }

    public function folder()
    {
        return config('enso.config.paths.files');
    }

    public function store(array $files)
    {
        $uploads = collect();

        \DB::transaction(function () use ($files, $uploads) {
            $existing = collect($files)->map(function ($file) {
                return $file->getClientOriginalName();
            })->intersect($this->existingUploads());

            if ($existing->isNotEmpty()) {
                throw new UploadException(__(
                    'File(s): :files already uploaded',
                    ['files' => $existing->implode(', ')]
                ));
            }

            collect($files)->each(function ($file) use ($uploads) {
                $upload = Upload::create();
                $upload->upload($file);
                $uploads->push(new Resource($upload->file->load(['attachable', 'createdBy'])));
            });
        });

        return $uploads;
    }

    private function existingUploads()
    {
        return File::forUser(auth()->user())
            ->whereAttachableType(self::class)
            ->pluck('original_name');
    }
}
