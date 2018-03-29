<?php

return [
    '/' => [
        'method' => ['GET'],
        'map' => 'IndexController@index'
    ],
    '/([a-z]+)/([a-z]+)' => [
        'method' => ['GET', 'POST', 'PUT', 'DELETE', 'PATCH'],
        'rule' => '{controller}{action}',
    ],
    '/api/([a-z]+)/([a-z]+)' => [
        'method' => ['GET', 'PUT', 'POST', 'OPTIONS'],
        'rule' => 'Api\{controller}{action}'
    ],
    '/api/([a-z]+)/([a-z]+)/([0-9]+)' => [
        'method' => ['GET', 'POST', 'OPTIONS'],
        'rule' => 'Api\{controller}{action}{id}'
    ]
];
