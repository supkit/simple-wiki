<?php

namespace App\Console\Crontab;

use Simple\Support\Log;

class Example
{
    public function handle()
    {
        // name = crontab:example
        Log::debug('crontab:start');
        Log::debug('crontab:end');
    }
}
