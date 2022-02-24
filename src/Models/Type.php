<?php

namespace LaravelEnso\Files\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use LaravelEnso\Rememberable\Traits\Rememberable;
use LaravelEnso\Tables\Traits\TableCache;

class Type extends Model
{
    use HasFactory, Rememberable, TableCache;

    protected $table = 'file_types';

    protected $guarded = [];

    protected $casts = ['is_browsable' => 'boolean', 'is_system' => 'boolean'];

    protected array $rememberableKeys = ['id', 'model'];

    public function files()
    {
        return $this->hasMany(File::class);
    }

    public function scopeBrowsable(Builder $query): Builder
    {
        return $query->whereIsBrowsable(true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderByDesc('is_system')->orderBy('id');
    }

    public static function for(string $model): self
    {
        return self::cacheGetBy('model', $model)
            ?? self::factory()->model($model)->create();
    }
}
