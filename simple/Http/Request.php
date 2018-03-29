<?php

namespace Simple\Http;

class Request
{
    /**
     * cURL 实例对象
     * @var
     */
    public $curl;

    /**
     * 相应结果
     * @var mixed
     */
    public $response;

    /**
     * cookie 信息
     * @var array
     */
    public $cookies = [];

    /**
     * 头信息
     * @var array
     */
    public $headers = [];

    /**
     * 响应头信息
     * @var array
     */
    public $responseHeaders = [];

    /**
     * 请求头信息
     * @var array
     */
    public $requestHeaders = [];

    /**
     * 错误状态
     * @var bool
     */
    public $error = false;

    /**
     * 错误嘛
     * @var int
     */
    public $errorCode = 0;

    /**
     * 错误信息
     * @var null
     */
    public $errorMessage = null;

    /**
     * cURL 错误状态
     * @var bool
     */
    public $curlError = false;

    /**
     * cURL 错误码
     * @var int
     */
    public $curlErrorCode = 0;

    /**
     * cURL 错误信息
     * @var null
     */
    public $curlErrorMessage = null;

    /**
     * HTTP 错误状态
     * @var bool
     */
    public $httpError = false;

    /**
     * HTTP 错误码
     * @var int
     */
    public $httpStatusCode = 0;

    /**
     * HTTP 错误信息
     * @var null
     */
    public $httpErrorMessage = null;

    /**
     * 禁止SSL验证
     * @var bool
     */
    public $prohibitSSLauth = false;

    /**
     * cURL 构造方法
     */
    public function __construct()
    {
        $this->init();
    }

    /**
     * 初始化
     * @return $this
     */
    private function init()
    {
        $this->curl = curl_init();

        // 发送请求的字符串
        $this->setOpt(CURLINFO_HEADER_OUT, true);

        // 是否输出响应头信息
        $this->setOpt(CURLOPT_HEADER, false);
        $this->setOpt(CURLOPT_RETURNTRANSFER, true);

        // 是否禁止SSL验证
        if ($this->prohibitSSLauth === true) {
            $this->setOpt(CURLOPT_SSL_VERIFYHOST, false);
            $this->setOpt(CURLOPT_SSL_VERIFYPEER, false);
        }

        return $this;
    }

    /**
     * 开始执行cURL的基础设置
     * @return int|mixed
     */
    private function execute()
    {
        $this->response = curl_exec($this->curl);
        $this->curlErrorCode = curl_errno($this->curl);
        $this->curlErrorMessage = curl_error($this->curl);

        $this->curlError = !($this->curlErrorCode === 0);
        $this->httpStatusCode = curl_getinfo($this->curl, CURLINFO_HTTP_CODE);
        $this->httpError = in_array(floor($this->httpStatusCode / 100), [4, 5]);

        $this->error = $this->curlError || $this->httpError;
        $this->errorCode = $this->error ? ($this->curlError ? $this->curlErrorCode : $this->httpStatusCode) : 0;

        $this->requestHeaders = preg_split('/\r\n/', curl_getinfo($this->curl, CURLINFO_HEADER_OUT), null, PREG_SPLIT_NO_EMPTY);

        $matchResult = preg_match('/HTTP\\/1.[0-9] [0-9]{3}.*OK/', $this->response);
        if (!(strpos($this->response, "\r\n\r\n") === false) && $matchResult) {
            list($responseHeader, $this->response) = explode("\r\n\r\n", $this->response, 2);
            while (strtolower(trim($responseHeader)) === 'http/1.1 100 continue') {
                list($responseHeader, $this->response) = explode("\r\n\r\n", $this->response, 2);
            }
            $this->responseHeaders = preg_split('/\r\n/', $responseHeader, null, PREG_SPLIT_NO_EMPTY);
        }

        $this->httpErrorMessage = $this->error ? (isset($this->responseHeaders['0']) ? $this->responseHeaders['0'] : '') : '';
        $this->errorMessage = $this->curlError ? $this->curlErrorMessage : $this->httpErrorMessage;
        return $this->errorCode;
    }

    /**
     * 发送GET请求
     * @param $url
     * @param array $data
     * @return $this
     */
    public function get($url, $data = [], $json = false)
    {
        // 转json
        if ($json === true) {
            $data = empty($data) ? '{}' : json_encode($data);
        }

        if (is_array($data) && count($data) > 0) {
            $this->setOpt(CURLOPT_URL, $url.'?'.http_build_query($data));
        } else {
            $this->setOpt(CURLOPT_URL, $url);
        }

        $this->setOpt(CURLOPT_HTTPGET, true);
        $this->execute();
        return $this;
    }

    /**
     * 发送POST请求
     * @param $url
     * @param array $data
     * @param bool $json
     * @return $this
     */
    public function post($url, $data = [], $json = false)
    {
        // 转json
        if ($json === true) {
            $data = empty($data) ? '{}' : json_encode($data);
        }

        $this->setOpt(CURLOPT_URL, $url);
        $this->setOpt(CURLOPT_POST, true);
        $this->setOpt(CURLOPT_POSTFIELDS, $data);
        $this->execute();
        return $this;
    }

    /**
     * @param $url
     * @param array $data
     * @param bool $json
     * @param bool $payload
     * @return $this
     */
    public function put($url, $data = [], $json = false, $payload = false)
    {
        if ($payload === true) {
            $url .= '?'.http_build_query($data);
        }

        // 转json
        if ($json === true) {
            $data = empty($data) ? '{}' : json_encode($data);
        }

        $this->setOpt(CURLOPT_URL, $url);
        $this->setOpt(CURLOPT_CUSTOMREQUEST, 'PUT');
        $this->setOpt(CURLOPT_POST, true);
        $this->setOpt(CURLOPT_POSTFIELDS, $data);
        $this->execute();
        return $this;
    }

    /**
     * @param $url
     * @param array $data
     * @param bool $payload
     * @return $this
     */
    public function patch($url, $data = [], $payload = false)
    {

        if ($payload === true) {
            $url .= '?'.http_build_query($data);
        }

        $this->setOpt(CURLOPT_URL, $url);
        $this->setOpt(CURLOPT_CUSTOMREQUEST, 'PATCH');
        $this->setOpt(CURLOPT_POST, true);
        $this->setOpt(CURLOPT_POSTFIELDS, $data);
        $this->execute();
        return $this;
    }

    /**
     * @param $url
     * @param array $data
     * @param bool $payload
     * @return $this
     */
    public function delete($url, $data = [], $payload = false)
    {
        if ($payload === true) {
            $url .= '?'.http_build_query($data);
        }

        $this->setOpt(CURLOPT_URL, $url);
        $this->setOpt(CURLOPT_CUSTOMREQUEST, 'DELETE');
        $this->setOpt(CURLOPT_POST, true);
        $this->setOpt(CURLOPT_POSTFIELDS, $data);
        $this->execute();
        return $this;
    }

    /**
     * 设置允许 cURL 函数执行的最长秒数
     * @param $second
     * @return $this
     */
    public function setTimeout($second)
    {
        $this->setOpt('CURLOPT_TIMEOUT', $second);
        return $this;
    }

    /**
     * 设置cURL 参数
     * @param $option
     * @param $value
     * @return bool
     */
    public function setOpt($option, $value)
    {
        return curl_setopt($this->curl, $option, $value);
    }

    /**
     * 设置请求头信息
     * @param $key
     * @param $value
     * @return $this
     */
    public function setHeader($key, $value)
    {
        $this->headers[$key] = $key.': '.$value;
        $this->setOpt(CURLOPT_HTTPHEADER, array_values($this->headers));
        return $this;
    }

    /**
     * 设置cookie
     * @param $key
     * @param $value
     * @return $this
     */
    public function setCookie($key, $value)
    {
        $this->cookies[$key] = $value;
        $this->setOpt(CURLOPT_COOKIE, http_build_query($this->cookies, '', '; '));
        return $this;
    }

    /**
     * 请求是否成功
     * @return bool
     */
    public function isSuccess()
    {
        return $this->httpStatusCode >=200 && $this->httpStatusCode < 300;
    }

    /**
     * 是否已经被重定向
     * @return bool
     */
    public function isRedirect()
    {
        return $this->httpStatusCode >= 300 && $this->httpStatusCode < 400;
    }

    /**
     * 客户端或者服务器端是否有错误
     * @return bool
     */
    public function isError()
    {
        return $this->httpStatusCode >= 400 && $this->httpStatusCode < 600;
    }

    /**
     * 客户端错误
     * @return bool
     */
    public function isClientError()
    {
        return $this->httpStatusCode >= 400 && $this->httpStatusCode < 500;
    }

    /**
     * 服务器端错误
     * @return bool
     */
    public function isServerError()
    {
        return $this->httpStatusCode >= 500 && $this->httpStatusCode < 600;
    }

    /**
     * 释放cURL资源
     * @return $this
     */
    private function close()
    {
        if (is_resource($this->curl)) {
            curl_close($this->curl);
        }

        return $this;
    }

    /**
     * 关闭cURL、释放资源
     */
    public function __destruct()
    {
        return $this->close();
    }
}
