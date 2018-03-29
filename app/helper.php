<?php

/**
 * 加载配置文件
 *
 * @param $file
 * @return array
 */
function config($file) {
    $file = __DIR__.'/../config/'.$file.'.php';
    return file_exists($file) ? include "{$file}" : [];
}

/**
 * 获取一个Redis实例
 *
 * @param string $connection
 * @return null|Redis|\Simple\Support\Redis
 * @throws Exception
 */
function redis($connection = 'master') {

    if (empty($redis)) {
        $redis = Simple\Support\Redis::instance($connection);
        return $redis;
    }

    return $redis;
}
