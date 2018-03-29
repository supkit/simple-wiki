<?php

namespace Simple\Database;

use PDOException;
use PDO;

class MySQL
{
    /**
     * 默认的PDO连接配置
     *
     * @var array
     */
    protected $options = [
        PDO::ATTR_CASE => PDO::CASE_NATURAL,
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_ORACLE_NULLS => PDO::NULL_NATURAL,
        PDO::ATTR_STRINGIFY_FETCHES => false,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];

    /**
     * 返回一个MySQL的连接实例
     *
     * @param array $config
     * @return PDO
     */
    public function connect(array $config)
    {
        $dsn = $this->getDsn($config);
        $options = $this->getOption($config);

        $connection = $this->createConnection($dsn, $config, $options);

        $collation = $config['collation'];

        if (isset($config['charset'])) {
            $charset = $config['charset'];

            $names = "set names '$charset'".
                (! is_null($collation) ? " collate '$collation'" : '');

            $connection->prepare($names)->execute();
        }

        if (isset($config['timezone'])) {
            $connection->prepare(
                'set time_zone="'.$config['timezone'].'"'
            )->execute();
        }

        $this->setModes($connection, $config);

        return $connection;
    }

    /**
     * 新建一个PDO的连接实例
     *
     * @param $dsn
     * @param $config
     * @param $options
     * @return PDO
     */
    public function createConnection($dsn, $config, $options)
    {
        $username = $config['username'];
        $password = $config['password'];
        try {
            $pdo = new PDO($dsn, $username, $password, $options);
        } catch (PDOException $exception) {
            throw $exception;
        }
        return $pdo;
    }

    /**
     * 获取连接配置
     *
     * @param array $config
     * @return array|mixed
     */
    public function getOption(array $config)
    {
        $options = $config['options'];
        return array_diff_key($this->options, $options) + $options;
    }

    /**
     * Get the DSN string for a host / port configuration.
     *
     * @param array $config
     * @return string
     */
    public function getDsn(array $config)
    {
        return isset($port)
            ? "mysql:host={$config['host']};port={$config['port']};dbname={$config['database']}"
            : "mysql:host={$config['host']};dbname={$config['database']}";
    }

    /**
     * 设置连接模块
     *
     * @param PDO  $connection
     * @param array  $config
     * @return void
     */
    protected function setModes(PDO $connection, array $config)
    {
        if (isset($config['modes'])) {
            $modes = implode(',', $config['modes']);

            $connection->prepare("set session sql_mode='".$modes."'")->execute();
        } elseif (isset($config['strict'])) {
            if ($config['strict']) {
                $connection->prepare("set session sql_mode='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION'")->execute();
            } else {
                $connection->prepare("set session sql_mode='NO_ENGINE_SUBSTITUTION'")->execute();
            }
        }
    }
}
