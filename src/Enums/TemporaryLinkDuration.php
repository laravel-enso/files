<?php

namespace LaravelEnso\Files\Enums;

use LaravelEnso\Enums\Contracts\Frontend;
use LaravelEnso\Enums\Contracts\Mappable;

enum TemporaryLinkDuration: int implements Mappable, Frontend
{
    case FiveMinutes = 5 * 60;
    case OneHour = 60 * 60;
    case OneDay = 60 * 60 * 24;

    public function map(): string
    {
        return match ($this) {
            self::FiveMinutes => '5m',
            self::OneHour => '1h',
            self::OneDay => '24h',
        };
    }

    public static function registerBy(): string
    {
        return 'temporaryLinkDuration';
    }
}
