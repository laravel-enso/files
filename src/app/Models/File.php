<?php

namespace LaravelEnso\FileManager\app\Models;

use Illuminate\Database\Eloquent\Model;
use LaravelEnso\TrackWho\app\Traits\CreatedBy;

class File extends Model
{
    use CreatedBy;

    protected $fillable = ['original_name', 'saved_name', 'size', 'mime_type'];

    public function attachable()
    {
        return $this->morphTo();
    }
}
