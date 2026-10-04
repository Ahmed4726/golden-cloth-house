<?php
use Illuminate\Support\Facades\Schedule;
Schedule::command('social:publish-scheduled')->everyMinute()->withoutOverlapping();
