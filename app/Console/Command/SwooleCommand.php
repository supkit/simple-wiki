<?php
/**
 * Created by PhpStorm.
 * User: simple
 * Date: 2018/3/13
 * Time: 上午11:50
 */

namespace App\Console\Command;

class SwooleCommand
{
    protected $server;

    public function handle()
    {
        $this->server = new \Swoole\Server('127.0.0.1', 9502);
        $this->server->set([
            'task_worker_num' => 2,
            'daemonize' => true,
            'log_file' => 'storage/swoole/work.log'
        ]);

        echo 'Run...' . PHP_EOL;

        // 启动时回调
        $this->server->on('start', [$this, 'start']);
        $this->server->on('receive', [$this, 'receive']);
        $this->server->on('task', [$this, 'task']);
        $this->server->on('finish', [$this, 'finish']);

        $this->server->start();
    }

    public function start($server)
    {
        echo 'Swoole '.SWOOLE_VERSION.' start successful' . PHP_EOL;
    }

    public function receive($server, $fd, $fromId, $data)
    {
        $data = json_decode($data, true);

        var_export($data);

        $server->task($data);
    }

    public function task($server, $taskId, $fromId, $data)
    {
        echo '开始任务处理'. PHP_EOL;

        $task = new $data['class']($data['data']);

        $data = $task->data;

        $this->message($task, $data);
        $server->finish($data);
    }

    public function finish($server, $taskId, $data)
    {
        echo '执行完成' . PHP_EOL;
    }

    private function message($task, $data)
    {
        $task->handle();
    }
}