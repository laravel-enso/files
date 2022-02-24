<?php

namespace LaravelEnso\Files\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use LaravelEnso\Users\Models\User;

class Favorite extends Model
{
    protected $table = 'favorite_files';

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function file()
    {
        return $this->belongsTo(File::class);
    }

    public static function toggle(User $user, File $file)
    {
        $isFavorite = ! static::for($user, $file)->first()?->delete();

        if ($isFavorite) {
            self::create([
                'user_id' => $user->id,
                'file_id' => $file->id,
            ]);
        }

        return $isFavorite;
    }

    public function scopeFor(Builder $query, User $user, File $file): Builder
    {
        return $query->whereUserId($user->id)
            ->whereFileId($file->id);
    }
}
