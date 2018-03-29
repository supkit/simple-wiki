<?php

namespace Simple\Cache;

class RedisCache
{
    /**
     * Redis database
     * @var int
     */
    protected $db = 0;

    /**
     * connection
     * @var string
     */
    protected $connection = 'master';

    /**
     * 缓存过期时间
     * @var float|int
     */
    protected $expireTime = 3600*24*2;

    /**
     * 默认缓存key值
     * @var string
     */
    protected $prefix = 'default:cache';

    /**
     * 更新标识
     * @var bool
     */
    protected $update = false;

    /**
     * 删除标识
     * @var bool
     */
    protected $delete = false;

    /**
     * 设置database
     *
     * RedisCache constructor.
     * @throws \Exception
     */
    public function __construct()
    {
        $this->getRedis()->select($this->db);
    }

    /**
     * 获取的一个Redis实例
     *
     * @return null|\Redis|\Simple\Support\Redis
     * @throws \Exception
     */
    public function getRedis()
    {
        return redis($this->connection);
    }

    /**
     * @param $cacheKey
     * @param $callback
     * @return int|mixed
     * @throws \Exception
     */
    public function call($cacheKey, $callback)
    {
        if (empty($cacheKey)) {
            $debug = debug_backtrace();
            $cacheKey = $this->prefix . ':'.$debug[1]['function'].':'.md5(serialize($debug[1]['args']));
        }

        if ($this->delete) {
            return $this->del($cacheKey);
        }

        $cached = $this->get($cacheKey);

        if ($cached && !$this->update) {
            return unserialize($cached);
        }

        $data = $callback();
        $this->set($cacheKey, serialize($data));
        return $data;
    }

    /**
     * @param $key
     * @param $value
     * @return bool
     * @throws \Exception
     */
    public function set($key, $value)
    {
        return $this->getRedis()->set($key, $value);
    }

    /**
     * @param $cacheKey
     * @return bool|string
     * @throws \Exception
     */
    public function get($cacheKey)
    {
        return $this->getRedis()->get($cacheKey);
    }

    /**
     * @param $cacheKey
     * @return int
     * @throws \Exception
     */
    public function del($cacheKey)
    {
        if (is_array($cacheKey)) {
            return $this->getRedis()->delete($cacheKey);
        }
        return $this->getRedis()->del($cacheKey);
    }

    /**
     * 删除缓存
     */
    public function delete()
    {
        $this->delete = true;
        return $this;
    }

    /**
     * 更新缓存
     */
    public function update()
    {
        $this->update = true;
        return $this;
    }

    /**
     * 清空当前类下的所有缓存
     *
     * @return bool|int
     * @throws \Exception
     */
    public function flush()
    {
        $keys = $this->getRedis()->keys($this->prefix . '*');

        if (is_array($keys)) {
            return $this->del($keys);
        }

        return false;
    }
}
