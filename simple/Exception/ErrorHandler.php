<?php

namespace Simple\Exception;

use ErrorException;
use Exception;
use Error;
use Simple\Http\Response;
use Simple\Support\Log;

class ErrorHandler
{
    const ERROR_CODE = 500;

    /**
     * http 错误信息
     * @var array
     */
    protected $httpErrorMessage = [
        '403' => '403 Forbidden',
        '404' => '404 Not Found',
        '500' => '500 Internal Server Error'
    ];

    /**
     * 异常处理
     *
     * @param callable $callback
     * @param bool $errorPage
     * @return bool|mixed
     */
    public function callback(callable $callback, $errorPage = false)
    {
        Facade::setErrorHandler(function ($level, $message, $file, $line) {
            throw new ErrorException($message, self::ERROR_CODE, $level, $file, $line);
        }, E_ALL | E_STRICT);

        $errorPage = empty($errorPage) ? __DIR__ . '/response/' : $errorPage;

        try {
            $callback();
        } catch (HttpException $httpException) {
            return $this->exception($httpException, $errorPage);
        } catch (ErrorException $exception) {
            return $this->exception($exception, $errorPage);
        } catch (Exception $exception) {
            return $this->exception($exception, $errorPage);
        } catch (Error $exception) {
            return $this->exception($exception, $errorPage);
        }

        return false;
    }

    /**
     * 输出异常信息并记录日志
     *
     * @param $exception Exception | HttpException | ErrorException
     * @param $errorPage
     * @return mixed
     */
    private function exception($exception, $errorPage)
    {
        if (method_exists($exception, 'getHttpStatusCode')) {
            $httpCode =  $exception->getHttpStatusCode();
            $page = "{$errorPage}http/{$httpCode}.php";
        } else {
            $httpCode = self::ERROR_CODE;
            $page = "{$errorPage}default.php";
        }

        $message = $exception->getMessage();
        $file = $exception->getFile();
        $line = $exception->getLine();
        $trace = $exception->getTrace();

        $httpMessage = $this->httpErrorMessage[$httpCode];

        $error = $message .' '.$file.':'.$line;
        $log = $error.$this->trace($trace);

        Log::error($log);
        return include $page;
    }

    /**
     * trace array to string
     *
     * @param array $trace
     * @return string
     */
    private function trace(array $trace) : string
    {
        $string = PHP_EOL;
        foreach ($trace as $item) {
            $class = empty($item['class']) ? '' : $item['class'];
            $function = empty($item['function']) ? '' : '::'.$item['function'];
            $file = empty($item['file']) ? '' : $item['file'].':';
            $line = empty($item['line']) ? '' : $item['line'];
            $string .= "# {$file}{$line} {$class}{$function}" . PHP_EOL;
        }

        return $string;
    }
}
