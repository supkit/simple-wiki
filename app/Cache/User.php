<?php

namespace App\Cache;

use Simple\Cache\RedisCache;
use App\Model\User as UserModel;
use Exception;
use ErrorException;

class User extends RedisCache
{
    /**
     * User Model
     *
     * @var User
     */
    protected $model;

    /**
     * 设置缓存使用的Redis database
     *
     * @var int
     */
    protected $db = 1;

    /**
     * 设置缓存前缀
     *
     * @var string
     */
    protected $prefix = 'app:user';

    /**
     * User constructor.
     * @throws ErrorException
     * @throws Exception
     */
    public function __construct()
    {
        parent::__construct();
        $this->model = new UserModel();
    }

    /**
     * @param int $page
     * @param int $limit
     * @return int|mixed
     * @throws Exception
     */
    public function list($page = 0, $limit = 10)
    {
        return $this->call(false, function () use ($page, $limit) {
            return $this->model->select(['id', 'mobile'])
                ->limit([$page, $limit])
                ->fetchAll();
        });
    }

    /**
     * @param int $id
     * @return int|mixed
     * @throws Exception
     */
    public function detail($id = 0)
    {
        return $this->call(false, function () use ($id) {
            return $this->model->find($id);
        });
    }
}
