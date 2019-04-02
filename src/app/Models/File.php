<?php

namespace LaravelEnso\FileManager\app\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use LaravelEnso\TrackWho\app\Traits\CreatedBy;
use LaravelEnso\FileManager\app\Enums\VisibleFiles;
use LaravelEnso\FileManager\app\Classes\FileManager;

class File extends Model
{
    use CreatedBy;

    const ExpiresIn = 60 * 60 * 24;

    protected $fillable = ['original_name', 'saved_name', 'size', 'mime_type'];

    public function attachable()
    {
        return $this->morphTo();
    }

    public function temporaryLink()
    {
        return url()->temporarySignedRoute(
            'core.files.share',
            now()->addSeconds(self::ExpiresIn),
            ['file' => $this->id]
        );
    }

    public function type()
    {
        return VisibleFiles::get($this->attachable_type);
    }

    public function path()
    {
        return storage_path(
            'app'.DIRECTORY_SEPARATOR.
            (app()->environment('testing')
                ? FileManager::TestingFolder
                : $this->attachable->folder())
            .DIRECTORY_SEPARATOR.$this->saved_name
        );
    }

    public function scopeVisible($query)
    {
        $query->whereIn('attachable_type', VisibleFiles::keys());
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
                config('enso.config.dateFormat'),
                $interval->min
            ));
        })->when(! empty($interval->max), function ($query) use ($interval) {
            $query->where('created_at', '<', Carbon::createFromFormat(
                config('enso.config.dateFormat'),
                $interval->max
            ));
        });
    }

    public function scopeFiltered($query, $search)
    {
        if (! empty($search)) {
            $query->where('original_name', 'LIKE', '%'.$search.'%');
        }
    }
}
