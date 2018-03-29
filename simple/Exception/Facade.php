<?php

namespace Simple\Exception;

class Facade
{
    /**
     * setErrorHandler
     *
     * @param callable $handler
     * @param $type
     * @return mixed
     */
    public static function setErrorHandler(callable $handler, $type)
    {
        return set_error_handler($handler, $type);
    }
}