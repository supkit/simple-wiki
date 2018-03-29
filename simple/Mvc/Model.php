<?php

namespace Simple\Mvc;

use Simple\Database\MySQL;
use Simple\Database\Query\Builder;
use ErrorException;

abstract class Model
{
    /**
     * 表名称
     *
     * @var null | string
     */
    protected $table = null;

    /**
     * 连接标志
     *
     * @var string
     */
    protected $connection = 'master';

    /**
     * 主键
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * @var null
     */
    protected $where = null;

    /**
     * @var null
     */
    protected $order = null;

    /**
     * @var null
     */
    protected $limit = null;

    /**
     * @var null
     */
    protected $group = null;

    /**
     * 连接单例
     *
     * @var null | \PDO
     */
    private static $db = null;

    /**
     * @var null | \PDOStatement
     */
    protected $statement = null;

    /**
     * 控制下一行如何返回给调用者
     *
     * @var int
     */
    protected $fetchStyle = \PDO::FETCH_ASSOC;

    /**
     * 是否启用软删除
     *
     * @var bool
     */
    protected $useSoftDelete = false;

    /**
     * 软删除字段名称
     *
     * @var string
     */
    const DELETED = 'deleted';

    /**
     * Model constructor.
     * @throws ErrorException
     */
    public function __construct()
    {
        self::createConnection($this->connection);
    }

    /**
     * @param $connection
     * @return null|\PDO
     * @throws ErrorException
     */
    public static function createConnection($connection)
    {
        $config = config('database');

        if (!isset($config[$connection])) {
            throw new ErrorException('MySQL connection error , the connection: '.$connection.' not exits!');
        }

        $config = $config[$connection];

        if (empty(self::$db)) {
            self::$db = (new MySQL())->connect($config);
        }

        return self::$db;
    }

    /**
     * @return null|\PDO
     */
    public function db()
    {
        return self::$db;
    }

    /**
     * 写入
     *
     * @param array $data 一维数组为单条写入，二维数组为多条写入
     * @param bool $id
     * @return bool|string
     */
    public function insert(array $data, $id = false)
    {
        $inserted = false;

        $keys = count($data) == count($data, COUNT_RECURSIVE)
            ? array_keys($data)
            : array_keys(current($data));

        $this->statement = $this->db()->prepare(
            $this->builder()->insert($this->getTable(), $keys)
        );

        // 单条写入
        if (count($data) == count($data, COUNT_RECURSIVE)) {
            $inserted = $this->statement->execute(array_values($data));
            return $id ? $this->db()->lastInsertId() : $inserted;
        }

        // 多条写入
        foreach ($data as $value) {
            $inserted = $this->statement->execute(array_values($value));
        }

        return $id ? $this->db()->lastInsertId() : $inserted;
    }

    /**
     * 更新
     *
     * @param array $data
     * @param bool $force
     * @return bool
     * @throws ErrorException
     */
    public function update(array $data, $force = false)
    {
        $values = array_merge(array_values($data), $this->builder()->value());

        if (!$force && empty($this->where)) {
            throw new ErrorException('Update need where condition');
        }

        $where = empty($this->where) ? '' : ' '.$this->builder()->whereFormat();

        $this->statement = $this->db()->prepare(
            $this->builder()->update($this->getTable(),
                array_keys($data)) . $where);

        return $this->statement->execute($values);
    }

    /**
     * 删除
     *
     * @param bool $force
     * @return bool
     * @throws ErrorException
     */
    public function delete($force = true)
    {
        if (!$force && empty($this->where)) {
            throw new ErrorException('Delete need where condition');
        }

        if ($this->useSoftDelete) {
            return $this->update([self::DELETED => date('Y-m-d H:i:s', time())]);
        }

        $where = empty($this->where) ? '' : ' '.$this->builder()->whereFormat();
        $this->statement = $this->db()->prepare($this->builder()->delete($this->getTable()). $where);
        return $this->statement->execute($this->builder()->value());
    }

    /**
     * 查询第一条数据
     *
     * @param array $column
     * @return array|mixed
     */
    public function first($column = [])
    {
        $this->limit = $this->builder()->limit(1);
        $select = $this->useSelect($column);

        $this->statement = $this->db()->prepare($select);
        $this->statement->execute($this->builder()->value());

        return $this->fetch();
    }

    /**
     * 根据主键查询一条
     *
     * @param $id
     * @param array $column
     * @return array|mixed
     */
    public function find($id, $column = [])
    {
        $this->where($this->primaryKey, '=', $id);
        $this->limit = $this->builder()->limit(1);

        $select = $this->useSelect($column);
        $this->statement = $this->db()->prepare($select);
        $this->statement->execute($this->builder()->value());

        return $this->fetch();
    }

    /**
     * 查询方法
     *
     * @param array $column
     * @return $this
     */
    public function select(array $column = [])
    {
        $select = $this->useSelect($column);
        $this->statement = $this->db()->prepare($select);
        $this->statement->execute($this->builder()->value());

        return $this;
    }

    /**
     * where
     *
     * @param $column
     * @param array ...$params
     * @return $this
     */
    public function where($column, ...$params)
    {
        $this->where = $this->builder()->where($column, ...$params);
        return $this;
    }

    /**
     * limit
     *
     * @param $limit
     * @return $this
     */
    public function limit($limit)
    {
        $this->limit = $this->builder()->limit($limit);
        return $this;
    }

    /**
     * order
     *
     * @param array $order
     * @return $this
     */
    public function order(array $order)
    {
        $this->order = $this->builder()->order($order);
        return $this;
    }

    /**
     * group
     *
     * @param array $group
     * @return $this
     */
    public function group(array $group)
    {
        $this->group = $this->builder()->group($group);
        return $this;
    }

    /**
     * 查询构造
     *
     * @param $column
     * @return string
     */
    private function useSelect($column)
    {
        if ($this->useSoftDelete) {
            $this->where(self::DELETED, 'is null');
        }

        $select = $this->builder()->select($this->getTable(), $column);

        $where = empty($this->where) ? '' : ' '.$this->builder()->whereFormat();
        $group = empty($this->group) ? '' : $this->group;
        $order = empty($this->order) ? '' : $this->order;
        $limit = empty($this->limit) ? '' : $this->limit;

        // $this->limit = ''; $this->order = ''; $this->group = ''; $this->where = '';
        $this->where = $this->limit = $this->order = $this->group = '';
        return $select . $where . $group . $order . $limit;
    }

    /**
     * 返回单条
     *
     * @return array|mixed
     */
    public function fetch()
    {
        $data = $this->statement->fetch($this->fetchStyle);
        return $data ? $data : [];
    }

    /**
     * 返回多条
     *
     * @return array
     */
    public function fetchAll()
    {
        $data = $this->statement->fetchAll($this->fetchStyle);
        return $data ? $data : [];
    }

    /**
     * 预处理查询
     *
     * @param $query
     * @param array $params
     * @return $this
     */
    public function query($query, array $params)
    {
        $this->statement = $this->db()->prepare($query);
        $this->statement->execute($params);

        return $this;
    }

    /**
     * @param bool $isArray
     * @return mixed
     */
    public function count($isArray = false)
    {
        $this->fetchStyle = $this->db()::FETCH_ASSOC;
        $count = $this->first(['count(*) as count']);
        return $isArray ? $count : $count['count'];
    }

    /**
     * 返回影响的行数
     *
     * @return int
     */
    public function rowCount()
    {
        return $this->statement->rowCount();
    }

    /**
     * 事务开始
     *
     * @return bool
     */
    public function begin()
    {
        return $this->db()->beginTransaction();
    }

    /**
     * 事务提交
     *
     * @return bool
     */
    public function commit()
    {
        return $this->db()->commit();
    }

    /**
     * 事务回滚
     *
     * @return bool
     */
    public function rollback()
    {
        return $this->db()->rollBack();
    }

    /**
     * 设置表名称
     *
     * @param $table
     * @return string
     */
    public function setTable($table)
    {
        return $this->table = $table;
    }

    /**
     * 获取表名称
     *
     * @return string
     */
    public function getTable()
    {
        if (empty($this->table)) {
            $className = explode('\\', get_class($this));
            $tableName = is_array($className) ? end($className) : $className;
            $this->table = strtolower(preg_replace('/((?<=[a-z])(?=[A-Z]))/', '_', $tableName));
        }

        return trim($this->table);
    }

    /**
     * 设置连接资源
     *
     * @param $connection
     */
    public function setConnection($connection)
    {
        $this->connection = $connection;
    }

    /**
     * 获取当前连接资源
     *
     * @return string
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * SQL拼接构造器
     *
     * @return null|Builder
     */
    public function builder()
    {
        return Builder::instance();
    }

    /**
     * SQL调试
     *
     * @return string
     */
    public function debug()
    {
        return $this->statement->queryString;
    }
}
