<?php

define('START', microtime(true));

require __DIR__.'/../vendor/autoload.php';
require __DIR__.'/../app/helper.php';

// 支持跨域访问
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: *');
header('Access-Control-Allow-Methods: *');
header('Access-Control-Allow-Credentials: true');

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    return true;
}

return (new Simple\App())->handle();
