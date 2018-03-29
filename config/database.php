<?php

return [
    'master' => [
        'driver'    => 'mysql',
        'host'      => $_ENV['db_host'],
        'username'  => $_ENV['db_username'],
        'password'  => $_ENV['db_password'],
        'database'  => $_ENV['db_database'],
        'charset'   => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
        'options'   => [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_CASE => PDO::CASE_NATURAL
        ]
    ],
    'user' => [
        'driver'    => 'mysql',
        'host'      => $_ENV['db_host'],
        'username'  => $_ENV['db_username'],
        'password'  => $_ENV['db_password'],
        'database'  => $_ENV['db_database'],
        'charset'   => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
        'options'   => [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_CASE => PDO::CASE_NATURAL
        ]
    ]
];
