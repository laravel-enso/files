<?php

namespace LaravelEnso\FileManager\app\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use LaravelEnso\TrackWho\app\Traits\CreatedBy;
use LaravelEnso\FileManager\app\Enums\VisibleFiles;

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
        if (! empty($interval->min)) {
            $query->where('created_at', '>', Carbon::parse($interval->min));
        }

        if (! empty($interval->max)) {
            $query->where('created_at', '<', Carbon::parse($interval->max));
        }
    }
}
