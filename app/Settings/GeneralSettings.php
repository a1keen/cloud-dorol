<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class GeneralSettings extends Settings
{
    public float|array|null $rate;
    public float|array|null $rate_time_series;

    public static function group(): string
    {
        return 'general';
    }
}
