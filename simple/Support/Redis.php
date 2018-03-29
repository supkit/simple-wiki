<?php

namespace Simple\Support;

use Redis as PhpRedis;
use Exception;

class Redis extends PhpRedis
{
    /**
     * @var null | PhpRedis
     */
    private static $instance = null;

    /**
     * Redis constructor.
     * @param $connection
     * @throws Exception
     */
    public function __construct($connection)
    {
        if (!extension_loaded('redis')) {
            throw new Exception('Redis extension not loaded');
        }

        $config = config('redis')[$connection];
        $this->connect($config['host'], $config['port']);

        if (!empty($config['password'])) {
            $this->auth($config['password']);
        }

        return $this;
    }

    /**
     * Redis 单例
     *
     * @param string $connection
     * @return null|PhpRedis|Redis
     * @throws Exception
     */
    public static function instance($connection = 'master')
    {
        if (empty(self::$instance)) {
            self::$instance = new self($connection);
        }

        return self::$instance;
    }
}
