<?php

namespace Simple\Support;

class Log
{
    /**
     * 系统不可用
     */
    const EMERGENCY = 'emergency';

    /**
     * 必须立刻采取行动
     */
    const ALERT     = 'alert';

    /**
     * 紧急情况
     */
    const CRITICAL  = 'critical';

    /**
     * 运行时出现的错误
     */
    const ERROR     = 'error';

    /**
     * 出现非错误性的异常
     */
    const WARNING   = 'warning';

    /**
     * 一般性重要的事件
     */
    const NOTICE    = 'notice';

    /**
     * 重要事件
     */
    const INFO      = 'info';

    /**
     * 调试
     */
    const DEBUG     = 'debug';

    /**
     * 系统不可用
     *
     * @param string $message
     * @param array $context
     * @return null
     */
    public static function emergency($message, array $context = array())
    {
        $level = self::EMERGENCY;
        return self::record($level, $message, $context);
    }

    /**
     * 必须立刻采取行动
     *
     * 例如：在整个网站都垮掉了、数据库不可用了或者其他的情况下，应该发送一条警报短信把你叫醒。
     *
     * @param string $message
     * @param array $context
     * @return null
     */
    public static function alert($message, array $context = array())
    {
        $level = self::ALERT;
        return self::record($level, $message, $context);
    }

    /**
     * 紧急情况
     *
     * 例如：程序组件不可用或者出现非预期的异常。
     *
     * @param string $message
     * @param array $context
     * @return null
     */
    public static function critical($message, array $context = array())
    {
        $level = self::CRITICAL;
        return self::record($level, $message, $context);
    }

    /**
     * 运行时出现的错误，不需要立刻采取行动，但必须记录下来以备检测。
     *
     * @param string $message
     * @param array $context
     * @return null
     */
    public static function error($message, array $context = array())
    {
        $level = self::ERROR;
        return self::record($level, $message, $context);
    }

    /**
     * 出现非错误性的异常。
     *
     * 例如：使用了被弃用的API、错误地使用了API或者非预想的不必要错误。
     *
     * @param string $message
     * @param array $context
     * @return null
     */
    public static function warning($message, array $context = array())
    {
        $level = self::WARNING;
        return self::record($level, $message, $context);
    }

    /**
     * 一般性重要的事件。
     *
     * @param string $message
     * @param array $context
     * @return null
     */
    public static function notice($message, array $context = array())
    {
        $level = self::NOTICE;
        return self::record($level, $message, $context);
    }

    /**
     * 重要事件
     *
     * 例如：用户登录和SQL记录。
     *
     * @param string $message
     * @param array $context
     * @return null
     */
    public static function info($message, array $context = array())
    {
        $level = self::INFO;
        return self::record($level, $message, $context);
    }

    /**
     * debug 详情
     *
     * @param string $message
     * @param array $context
     * @return null
     */
    public static function debug($message, array $context = array())
    {
        $level = self::DEBUG;
        return self::record($level, $message, $context);
    }

    /**
     * 记录日志
     * @param $level
     * @param $message
     * @param array $context
     * @return bool
     */
    private static function record($level, $message, $context = [])
    {
        list($year, $month, $day, $hour) = explode('-', date('Y-m-d-H', time()));
        $path = __DIR__ . '/../../storage/log/' . "{$year}/{$month}/{$day}/";

        $dateTime = date('Y-m-d H:i:s', time());

        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }

        $file = $path.$hour.'H.log';

        $responseTime = microtime(true) - START;
        $content = "[$dateTime] {$level}: {$message} {$responseTime}".PHP_EOL;

        file_put_contents($file, $content, FILE_APPEND);
        return true;
    }
}