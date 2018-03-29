#!/usr/bin/env php
<?php

define('START', microtime(true));

require __DIR__.'/vendor/autoload.php';
require __DIR__.'/app/helper.php';

$config = config('console');

if (PHP_SAPI != 'cli') {
    return false;
}

return new Simple\Console\Command($config);
