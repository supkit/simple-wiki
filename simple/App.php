<?php

namespace Simple;

use Simple\Exception\ErrorHandler;

class App
{
    /**
     * App constructor.
     */
    public function __construct() {}

    /**
     * Run frame
     * @return bool|Route
     */
    public function handle()
    {
        $errorHandler = new ErrorHandler();

        $errorHandler->callback(function () {
            return $this->run();
        });

        return false;
    }

    /**
     * @return Route
     * @throws Exception\HttpException
     * @throws \ErrorException
     */
    public function run()
    {
        $this->env();
        if (!empty($_ENV['timezone'])) {
            date_default_timezone_set($_ENV['timezone']);
        }

        return new Route(config('route'));
    }

    /**
     * 加载ENV
     */
    private function env()
    {
        $_ENV = array_merge($_ENV, config('env'));
    }
}
