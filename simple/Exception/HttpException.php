<?php

namespace Simple\Exception;
use Exception;

class HttpException extends Exception
{
    /**
     * 默认错误码
     * @var int
     */
    private $httpStatusCode = 500;

    /**
     * HttpException constructor.
     * @param int $httpCodeStatus
     * @param string $message
     * @param int $code
     * @param Exception|null $previous
     */
    public function __construct($httpCodeStatus = 500, $message = "", $code = 0, Exception $previous = null)
    {
        $this->httpStatusCode = $httpCodeStatus;
        parent::__construct($message, $code, $previous);
    }

    /**
     * 获取Http Code
     * @return int
     */
    public function getHttpStatusCode()
    {
        return $this->httpStatusCode;
    }
}
