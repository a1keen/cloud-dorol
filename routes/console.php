<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('app:update-rate')->everyMinute();

Schedule::command('app:update-rate-history')->daily();

