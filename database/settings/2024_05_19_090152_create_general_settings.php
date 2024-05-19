<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('general.rate', [
            'usd' => 0,
            'uah' => 0
        ]);

        $this->migrator->add('general.rate_time_series', []);
    }
};
