<?php

return [
    // 命令行执行
    'swoole' => App\Console\Command\SwooleCommand::class,

    // 定时任务
    'crontab:example' => App\Console\Crontab\Example::class
];
