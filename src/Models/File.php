<?php

namespace LaravelEnso\Files\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\File as IlluminateFile;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use LaravelEnso\Core\Models\User;
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

    public function temporaryLink(): string
    {
        $limit = Config::get('enso.files.linkExpiration');
        $expires = Carbon::now()->addSeconds($limit);
        $args = ['core.files.share', $expires, ['file' => $this->id]];

        return URL::temporarySignedRoute(...$args);
    }

    public function type(): string
    {
        return FileBrowser::folder($this->attachable_type);
    }

    public function path(): string
    {
        return Storage::path($this->path);
    }

    public function scopeBrowsable(Builder $query): Builder
    {
        return $query->whereIn('attachable_type', FileBrowser::models());
    }

    public function scopeFor(Builder $query, User $user): Builder
    {
        $inferiorRole = ! $user->isAdmin() && ! $user->isSupervisor();

        return $query->when($inferiorRole, fn ($query) => $query
            ->whereCreatedBy($user->id));
    }

    public function scopeBetween(Builder $query, array $interval): Builder
    {
        return $query
            ->when($interval['min'], fn ($query) => $query
                ->where('created_at', '>=', Carbon::parse($interval['min'])))
            ->when($interval['max'], fn ($query) => $query
                ->where('created_at', '<=', Carbon::parse($interval['max'])));
    }

    public function scopeFilter(Builder $query, ?string $search): Builder
    {
        return $query->when($search, fn ($query) => $query
            ->where('original_name', 'LIKE', '%'.$search.'%'));
    }

    public function attach(string $path, string $filename): self
    {
        $file = new IlluminateFile(Storage::path($path));

        $this->fill([
            'original_name' => $filename,
            'path' => $path,
            'size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
        ])->save();

        return $this;
    }

    public function upload(UploadedFile $file): self
    {
        if (! $file->isValid()) {
            throw Exception::uploadError($file->getClientOriginalName());
        }

        $this->validate($file);

        if ($this->isImage($file)) {
            $this->processImage($file);
        }

        return $this->store($file, $this->attachable->folder());
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

    public function validate(BaseFile $file): void
    {
        $extensions = $this->attachable->extensions();
        $mimeTypes = $this->attachable->mimeTypes();

        (new FileValidator($file, $extensions, $mimeTypes))->handle();
    }

    public function processImage(BaseFile $file): void
    {
        $optimizeImages = $this->attachable->optimizeImages();
        $resizeImages = $this->attachable->resizeImages();

        (new ImageProcessor($file, $optimizeImages, $resizeImages))->handle();
    }

    public function isImage(BaseFile $file): bool
    {
        $mimeTypes = implode(',', ImageTransformer::SupportedMimeTypes);

        return Validator::make(
            ['file' => $file],
            ['file' => "image|mimetypes:{$mimeTypes}"]
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
