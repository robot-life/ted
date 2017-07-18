<?php

use App\Schedule;
use Carbon\Carbon;

function create($class, $attributes = [], $times = null)
{
    return factory($class, $times)->create($attributes);
}

function make($class, $attributes = [], $times = null)
{
    return factory($class, $times)->make($attributes);
}

function openSchedule()
{
    Schedule::create([
        'strm' => '1179',
        'start' => (new Carbon)->subMonth(),
        'finish' => (new Carbon)->addMonth(),
    ]);
}
