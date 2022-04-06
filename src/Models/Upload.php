<?php

namespace LaravelEnso\Files\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use LaravelEnso\Files\Contracts\Attachable;
use LaravelEnso\Files\Contracts\CascadesFileDeletion;
use LaravelEnso\Files\Http\Resources\File as Resource;

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

    public static function store(array $files)
    {
        return DB::transaction(fn () => Collection::wrap($files)
            ->map(fn ($file) => self::upload($file)))
            ->values();
    }

    private static function upload($file): Resource
    {
        $upload = self::create();
        $file = File::upload($upload, $file);
        $upload->file()->associate($file)->save();
        $file->load('createdBy.person', 'createdBy.avatar', 'type');

        return new Resource($file);
    }
}
