<?php

namespace LaravelEnso\Files\app\Models;

use Illuminate\Database\Eloquent\Model;
use LaravelEnso\Files\app\Traits\HasFile;
use LaravelEnso\Files\app\Contracts\Attachable;
use LaravelEnso\Files\app\Contracts\VisibleFile;
use LaravelEnso\Files\app\Services\UploadManager;

class Upload extends Model implements Attachable, VisibleFile
{
    use HasFile;

    protected $optimizeImages = true;

    public function store(array $files)
    {
        return (new UploadManager($this, $files))
            ->handle();
    }

    public function isDeletable(): bool
    {
        return request()->user()
            ->can('handle', $this->file);
    }
}
