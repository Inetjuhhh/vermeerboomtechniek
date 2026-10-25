<?php

use Illuminate\Support\Facades\Schedule;

// Removes contact messages older than 12 months (promised on the privacy page).
// Needs the scheduler cron on the server: * * * * * php artisan schedule:run
Schedule::command('model:prune')->daily();
