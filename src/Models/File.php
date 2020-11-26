<?php

namespace LaravelEnso\Files\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\File as IlluminateFile;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use LaravelEnso\Core\Models\User;
use LaravelEnso\Files\Facades\FileBrowser;
use LaravelEnso\Files\Services\Files;
use LaravelEnso\Files\Traits\FilePolicies;
use LaravelEnso\TrackWho\Traits\CreatedBy;

class File extends Model
{
    use CreatedBy, FilePolicies;

    protected $guarded = ['id'];

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
        return Storage::disk($this->disk)->path($this->path);
    }

    public function scopeVisible($query)
    {
        $query->whereIn('attachable_type', FileBrowser::models()->toArray());
    }

    public function scopeForUser($query, $user)
    {
        $query->when(! $user->isAdmin() && ! $user->isSupervisor(), fn ($query) => $query
            ->whereCreatedBy($user->id));
    }

    public function scopeOrdered($query)
    {
        $query->orderBy('created_at', 'desc');
    }

    public function scopeBetween($query, $interval)
    {
        $query->when($interval->min, fn ($query) => $query->where(
            'created_at', '>=', Carbon::parse($interval->min)
        ))->when($interval->max, fn ($query) => $query->where(
            'created_at', '<=', Carbon::parse($interval->max)
        ));
    }

    public function scopeFilter($query, $search)
    {
        return $query->when($search, fn ($query) => $query
            ->where('original_name', 'LIKE', '%'.$search.'%'));
    }

    public function attach(IlluminateFile $file, string $originalName, ?User $user): void
    {
        (new Files($this->attachable))->attach($file, $originalName, $user);
    }

    public function upload(UploadedFile $file): void
    {
        (new Files($this->attachable))
            ->mimeTypes($this->attachable->mimeTypes())
            ->extensions($this->attachable->extensions())
            ->optimize($this->attachable->optimizeImages())
            ->resize($this->attachable->resizeImages())
            ->upload($file);
    }

    public function delete()
    {
        return DB::transaction(function () {
            $result = parent::delete();

            Storage::disk($this->disk)->delete($this->path);

            return $result;
        });
    }

    public function download()
    {
        return Storage::disk($this->disk)
            ->download(
                $this->path,
                Str::ascii($this->original_name)
            );
    }

    public function inline()
    {
        return Storage::disk($this->disk)
            ->response($this->path);
    }
}
