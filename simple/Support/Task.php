<?php

namespace Simple\Support;

class Task
{
    /**
     * Task constructor.
     * @param $task
     */
    public function __construct($task)
    {
        $data = $task->data;

        echo $class = get_class($task);

        $send['class'] = $class;
        $send['data'] = $data;

        $client = new \Swoole\Client(SWOOLE_SOCK_TCP);
        $client->connect('127.0.0.1', 9502);
        $client->send(json_encode($send));
    }
}