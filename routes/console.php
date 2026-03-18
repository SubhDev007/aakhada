<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

use Illuminate\Support\Facades\Schedule;

Schedule::command('game:start')->everyThirtySeconds()->withoutOverlapping();
Schedule::command('game:calculate-result')->everySecond()->withoutOverlapping();

