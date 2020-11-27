<?php

namespace LaravelEnso\Files\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\File as IlluminateFile;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use LaravelEnso\Core\Models\User;
use LaravelEnso\Files\Contracts\Attachable;
use LaravelEnso\Files\Exceptions\File as Exception;
use LaravelEnso\Files\Facades\FileBrowser;
use LaravelEnso\Files\Services\FileValidator;
use LaravelEnso\Files\Services\ImageProcessor;
use LaravelEnso\Files\Traits\FilePolicies;
use LaravelEnso\ImageTransformer\Services\ImageTransformer;
use LaravelEnso\TrackWho\Traits\CreatedBy;
use Symfony\Component\HttpFoundation\File\File as BaseFile;

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
        return Storage::path($this->path);
    }

    public function scopeVisible($query) //TODO browsable
    {
        $query->whereIn('attachable_type', FileBrowser::models()->toArray());
    }

    public function scopeForUser($query, $user)
    {
        $query->when(! $user->isAdmin() && ! $user->isSupervisor(), fn ($query) => $query
            ->whereCreatedBy($user->id));
    }

    public function scopeOrdered($query) //TODO replace cu latest
    {
        $query->orderBy('created_at', 'desc');
    }

    public function scopeBetween($query, $interval)
    {
        $query
            ->when($interval->min, fn ($query) => $query
                ->where('created_at', '>=', Carbon::parse($interval->min)))
            ->when($interval->max, fn ($query) => $query
                ->where('created_at', '<=', Carbon::parse($interval->max)));
    }

    public function scopeFilter($query, $search)
    {
        return $query->when($search, fn ($query) => $query
            ->where('original_name', 'LIKE', '%'.$search.'%'));
    }

    public function attach(string $path, string $originalName, ?User $user): self
    {
        $file = new IlluminateFile(Storage::path($path));

        $this->fill([
            'original_name' => $originalName,
            'path' => $path,
            'size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'created_by' => optional($user)->id,
        ])->save();

        return $this;
    }

    public function upload(Attachable $attachable, UploadedFile $file): self
    {
        if (! $file->isValid()) {
            throw Exception::uploadError($file->getClientOriginalName());
        }

        $this->validate($file, $attachable->extensions(), $attachable->mimeTypes());

        if ($this->isImage($file)) {
            $this->processImage($file, $attachable->optimizeImages(), $attachable->resizeImages());
        }

        return $this->store($file, $attachable->folder());
    }

    public function delete()
    {
        Storage::delete($this->path);

        return parent::delete();
    }

    public function download()
    {
        $name = Str::ascii($this->original_name);

        return Storage::download($this->path, $name);
    }

    public function inline()
    {
        return Storage::response($this->path);
    }

    public function validate(BaseFile $file, array $extensions, array $mimeTypes): self
    {
        (new FileValidator($file, $extensions, $mimeTypes))->handle();

        return $this;
    }

    public function processImage(BaseFile $file, bool $optimize, array $resize): self
    {
        (new ImageProcessor($file, $optimize, $resize))->handle();

        return $this;
    }

    public function isImage(BaseFile $file): bool
    {
        $mimeTypes = implode(',', ImageTransformer::SupportedMimeTypes);

        return Validator::make(
            ['file' => $file],
            ['file' => 'image|mimetypes:'.$mimeTypes]
        )->passes();
    }

    public function store(UploadedFile $file, string $folder): self
    {
        DB::transaction(function () use ($file, $folder) {
            $this->fill([
                'original_name' => $file->getClientOriginalName(),
                'path' => "{$folder}/{$file->hashName()}",
                'size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
            ])->save();

            $file->store($folder);
        });

        return $this;
    }
}
