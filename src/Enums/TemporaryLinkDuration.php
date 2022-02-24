<?php

namespace LaravelEnso\Files\Enums;

use LaravelEnso\Enums\Services\Enum;

class TemporaryLinkDuration extends Enum
{
    public const FiveMinutes = 5 * 60;
    public const OneHour = 60 * 60;
    public const OneDay = 60 * 60 * 24;

    public static function data(): array
    {
        return [
            self::FiveMinutes => '5m',
            self::OneHour => '1h',
            self::OneDay => '24h',
        ];
    }
}
