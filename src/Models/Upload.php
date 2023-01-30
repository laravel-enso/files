<?php

namespace LaravelEnso\Files\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use LaravelEnso\Files\Contracts\Attachable;
use LaravelEnso\Files\Contracts\CascadesFileDeletion;

class Upload extends Model implements Attachable, CascadesFileDeletion
{
    protected $guarded = [];

    public function file(): Relation
    {
        return $this->belongsTo(File::class);
    }

    public static function cascadeFileDeletion(File $file): void
    {
        self::whereFileId($file->id)->first()->delete();
    }

    public static function store(array $files): Collection
    {
        return DB::transaction(fn () => Collection::wrap($files)
            ->map(fn ($file) => self::upload($file)))
            ->values();
    }

    protected static function upload(UploadedFile $file): File
    {
        $upload = self::create();
        $file = File::upload($upload, $file);
        $upload->file()->associate($file)->save();

        return $upload->file;
    }
}
