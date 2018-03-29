<?php

namespace Simple\Console;

use ErrorException;
use Simple\Exception\ErrorHandler;
use Simple\Http\Response;

class Command
{
    /**
     * cmd config
     * @var array
     */
    private $config = [];

    /**
     * Command constructor.
     * @param $config
     */
    public function __construct($config)
    {
        $this->config = $config;

        $errorHandler = new ErrorHandler();

        $errorHandler->callback(function () {
            return $this->handle();
        });
    }

    /**
     * @return Response
     * @throws ErrorException
     */
    public function handle()
    {
        array_shift($_SERVER['argv']);
        $cmd = array_shift($_SERVER['argv']);
        $params = empty($_SERVER['argv']) ? [] : $_SERVER['argv'];

        $this->env();

        if (!empty($_ENV['timezone'])) {
            date_default_timezone_set($_ENV['timezone']);
        }

        if (!in_array($cmd, array_keys($this->config))) {
            throw new ErrorException('This command not found');
        }

        $object = new $this->config[$cmd];
        $response = call_user_func_array([$object, 'handle'], $params);

        return new Response($response);
    }

    /**
     * 加载ENV
     */
    private function env()
    {
        $_ENV = array_merge($_ENV, config('env'));
    }
}