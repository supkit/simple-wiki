<?php

namespace Simple\Database\Query;

class Builder
{
    /**
     * insert format
     */
    const INSERT_FORMAT = 'INSERT INTO `%s` (%s) VALUES (%s)';

    /**
     * update format
     */
    const UPDATE_FORMAT = 'UPDATE `%s` SET %s';

    /**
     * delete format
     */
    const DELETE_FORMAT = 'DELETE FROM `%s`';

    /**
     * select format
     */
    const SELECT_FORMAT = 'SELECT %s FROM `%s`';

    /**
     * where format
     */
    const WHERE_FORMAT = '%s %s %s %s';

    /**
     * limit format
     */
    const LIMIT_FORMAT = ' LIMIT %s';

    /**
     * order by format
     */
    const ORDER_BY_FORMAT = ' ORDER BY %s';

    /**
     * group by format
     */
    const GROUP_BY_FORMAT = ' GROUP BY %s';

    /**
     * 单例
     *
     * @var null | self
     */
    private static $instance = null;

    /**
     * @var array
     */
    private $value = [];

    /**
     * @var string
     */
    private $where = '';

    /**
     * Builder constructor.
     */
    private function __construct() {}

    /**
     * 单例
     * @return null|Builder
     */
    public static function instance()
    {
        if (empty(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * 拼接写入SQL
     *
     * @param $table
     * @param array $keys
     * @return string
     */
    public function insert($table, array $keys)
    {
        return vsprintf(self::INSERT_FORMAT, [$table, '`'. implode('`, `', $keys) . '`', rtrim(
            implode(", ", array_fill(0, count($keys), '?')), ',')
        ]);
    }

    /**
     * 拼接更新SQL
     *
     * @param $table
     * @param array $keys
     * @return string
     */
    public function update($table, array $keys)
    {
        $set = '`' . implode('` = ?, `', $keys) .'` = ?';
        return vsprintf(self::UPDATE_FORMAT, [$table, $set]);
    }

    /**
     * 拼接删除SQL
     *
     * @param $table
     * @return string
     */
    public function delete($table)
    {
        return vsprintf(self::DELETE_FORMAT, [$table]);
    }

    /**
     * where
     *
     * @param $column
     * @param array ...$params
     * @return string
     */
    public function where($column, ...$params)
    {
        $opera = array_shift($params);
        $value = array_shift($params);
        $connector = array_shift($params);

        $connector = empty($connector) ? ' AND' : ' '.$connector;

        $column = '`'.$column.'`';
        $value = is_null($value) ? [] : $value;

        $this->value = is_array($value) ?
            array_merge($this->value, $value) : array_merge($this->value, [$value]);

        $placeholder = is_array($value) && empty($value) ? '' : '?';

        // 支持IN NOT IN
        if (is_array($value) && in_array($opera, ['IN', 'NOT IN'])) {
            $placeholder = '('.implode(', ', array_fill(0, count($value), '?')).')';
        }

        return $this->where .= vsprintf(self::WHERE_FORMAT, [$connector, $column, $opera, $placeholder]);
    }

    /**
     * @return mixed|string
     */
    public function whereFormat()
    {
        $this->where = 'WHERE'.$this->where;
        $this->where = str_replace('WHERE AND', 'WHERE', $this->where);
        $this->where = str_replace('WHERE OR', 'WHERE', $this->where);
        $where = $this->where;
        $this->where = '';
        return $where;
    }

    /**
     * 拼接select
     *
     * @param $table
     * @param array $column
     * @return string
     */
    public function select($table, $column = [])
    {
        $column = empty($column) ? '*' : implode(', ', $column);
        return vsprintf(self::SELECT_FORMAT, [$column, $table]);
    }

    /**
     * limit
     *
     * @param $params
     * @return string
     */
    public function limit($params)
    {
        $limit = is_array($params) ? '?, ?' : '?';
        $this->value = is_array($params) ?
            array_merge($this->value, $params) :
            array_merge($this->value, [$params]);
        return vsprintf(self::LIMIT_FORMAT, [$limit]);
    }

    /**
     * order
     *
     * @param $params
     * @return string
     */
    public function order($params)
    {
        $format = implode(', ', array_map(function ($key, $value) {
            return $key .' '. $value;
        }, array_keys($params), array_values($params)));

        return vsprintf(self::ORDER_BY_FORMAT, [$format]);
    }

    /**
     * group
     * @param $params
     * @return string
     */
    public function group($params)
    {
        $format = implode(' ', $params);
        return vsprintf(self::GROUP_BY_FORMAT, [$format]);
    }

    /**
     * bind value
     *
     * @return array
     */
    public function value()
    {
        $value = $this->value;
        $this->value = [];
        return $value;
    }
}
