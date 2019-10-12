<?php

namespace LaravelEnso\Files\app\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use LaravelEnso\Files\app\Facades\FileBrowser;
use LaravelEnso\Files\app\Traits\FilePolicies;
use LaravelEnso\TrackWho\app\Traits\CreatedBy;

class File extends Model
{
    use CreatedBy, FilePolicies;

    protected $fillable = ['original_name', 'saved_name', 'size', 'mime_type'];

    public function attachable()
    {
        return $this->morphTo();
    }

    public function temporaryLink()
    {
        return url()->temporarySignedRoute(
            'core.files.share',
            now()->addSeconds(config('enso.files.linkExpiration')),
            ['file' => $this->id]
        );
    }

    public function type()
    {
        return FileBrowser::folder($this->attachable_type);
    }

    public function path()
    {
        return Storage::path(
            $this->attachable->folder()
            .DIRECTORY_SEPARATOR
            .$this->saved_name
        );
    }

    public function scopeVisible($query)
    {
        $query->hasMorph(
            'attachable',
            FileBrowser::models()->toArray()
        );
    }

    public function scopeForUser($query, $user)
    {
        return $user->isAdmin() || $user->isSupervisor()
            ? $query
            : $query->whereCreatedBy($user->id);
    }

    public function scopeOrdered($query)
    {
        $query->orderBy('created_at', 'desc');
    }

    public function scopeBetween($query, $interval)
    {
        $query->when(! empty($interval->min), function ($query) use ($interval) {
            $query->where('created_at', '>', Carbon::createFromFormat(
                config('enso.config.dateTimeFormat'),
                $interval->min
            ));
        })->when(! empty($interval->max), function ($query) use ($interval) {
            $query->where('created_at', '<', Carbon::createFromFormat(
                config('enso.config.dateTimeFormat'),
                $interval->max
            ));
        });
    }

    public function scopeFilter($query, $search)
    {
        if (! empty($search)) {
            $query->where('original_name', 'LIKE', '%'.$search.'%');
        }
    }
}
