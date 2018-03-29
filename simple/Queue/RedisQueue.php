<?php

namespace Simple\Queue;

use ErrorException;

class RedisQueue
{
    /**
     * 队列名称
     * @var null | string
     */
    protected $queueName = null;

    /**
     * @var int
     */
    protected $sleep = 5;

    /**
     * Redis实例
     * @var null|\Simple\Support\Redis
     */
    public $redis = null;

    /**
     * RedisQueue constructor.
     * @param string $connection
     */
    public function __construct($connection = 'queue')
    {
        $this->redis = $this->getRedis($connection);
    }

    /**
     * 获取一个redis实例
     *
     * @param $connection
     * @return \Simple\Support\Redis
     */
    public function getRedis($connection)
    {
        return redis($connection);
    }

    /**
     * push
     *
     * @param $data
     * @throws ErrorException
     * @return int
     */
    public function dispatch($data)
    {
        if (empty($this->queueName)) {
            throw new ErrorException('Queue name not found');
        }

        return $this->redis->rPush($this->queueName, $data);
    }

    /**
     * pop
     *
     * @return string
     * @throws ErrorException
     */
    public function work()
    {
        if (empty($this->queueName)) {
            throw new ErrorException('Queue name not found');
        }

        $length = $this->redis->lLen($this->queueName);

        if (!empty($length)) {
            return $this->redis->lPop($this->queueName);
        }

        sleep($this->sleep);
        return false;
    }
}
