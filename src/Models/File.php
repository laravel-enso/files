<?php

namespace LaravelEnso\Files\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\File as IlluminateFile;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use LaravelEnso\Files\Contracts\Attachable;
use LaravelEnso\Files\Services\ImageProcessor;
use LaravelEnso\Files\Services\Upload;
use LaravelEnso\ImageTransformer\Services\ImageTransformer;
use LaravelEnso\TrackWho\Traits\CreatedBy;
use LaravelEnso\Users\Models\User;
use Symfony\Component\HttpFoundation\File\File as BaseFile;
use Symfony\Component\HttpFoundation\StreamedResponse;

class File extends Model
{
    use CreatedBy;

    protected $guarded = ['id'];

    public function attachable()
    {
        return $this->morphTo();
    }

    public function type()
    {
        return $this->belongsTo(Type::class);
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    public function favorite()
    {
        return $this->hasOne(Favorite::class)
            ->whereUserId(Auth::id());
    }

    public function temporaryLink(?int $minutes = null): string
    {
        $limit = $minutes ?? Config::get('enso.files.linkExpiration');
        $expires = Carbon::now()->addSeconds($limit);
        $args = ['core.files.share', $expires, ['file' => $this->id]];

        return URL::temporarySignedRoute(...$args);
    }

    public function scopeBrowsable(Builder $query): Builder
    {
        return $query->whereHas('type', fn ($type) => $type->browsable());
    }

    public function scopeWithData(Builder $query): Builder
    {
        $attrs = ['type', 'createdBy.person', 'createdBy.avatar', 'favorite'];

        return $query->with($attrs);
    }

    public function scopeFor(Builder $query, User $user): Builder
    {
        $super = $user->isAdmin() || $user->isSupervisor();

        return $query->browsable()->withData()
            ->when(! $super, fn ($query) => $query->whereCreatedBy($user->id));
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

    public function name(): string
    {
        return Str::ascii($this->original_name);
    }

    public function folder(): string
    {
        return $this->type->folder;
    }

    public function path(): string
    {
        return "{$this->folder()}/{$this->saved_name}";
    }

    public static function attach(Attachable $attachable, string $savedName, string $filename, ?int $createdBy): self
    {
        $type = Type::for($attachable::class);
        $file = new IlluminateFile(Storage::path("{$type->folder}/{$savedName}"));

        return self::create([
            'type_id' => $type->id,
            'original_name' => $filename,
            'saved_name' => $savedName,
            'size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'created_by' => $createdBy,
        ]);
    }

    public static function upload(Attachable $attachable, UploadedFile $file): self
    {
        return (new Upload($attachable, $file))->handle();
    }

    public function delete(bool $parent = false)
    {
        if ($parent) {
            $this->type->model::whereFileId($this->id)->first()->delete();
        }

        $this->favorites->each->delete();

        Storage::delete($this->path());

        return parent::delete();
    }

    public function download(): StreamedResponse
    {
        return Storage::download($this->path(), $this->name());
    }

    public function inline(): StreamedResponse
    {
        return Storage::response($this->path());
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
}
