<?php

namespace LaravelEnso\Files;

use LaravelEnso\Enums\EnumServiceProvider as ServiceProvider;
use LaravelEnso\Files\Enums\TemporaryLinkDuration;

class EnumServiceProvider extends ServiceProvider
{
    public $register = [
        'temporaryLinkDuration' => TemporaryLinkDuration::class,
    ];
}
