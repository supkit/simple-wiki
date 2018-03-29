<?php

namespace Simple;

use Simple\Exception\HttpException;
use ErrorException;
use Simple\Http\Response;

class Route
{
    /**
     * 默认命名空间
     * @var string
     */
    protected $namespace = 'App\\Controller\\';

    /**
     * Route constructor.
     * @param $rules array 路由规则
     * @throws ErrorException
     * @throws HttpException
     * @return bool
     */
    public function __construct($rules)
    {
        $requestUri = $this->getRequestUri();

        foreach ($rules as $key => $value) {
            if (!preg_match('#^'.$key.'$#', $requestUri, $match)) {
                continue;
            }

            // 访问方式限制
            if (!empty($value['method'])) {
                $this->allowMethod($value['method']);
            }

            // 映射路由
            if (!empty($value['map'])) {
                return $this->mapRoute($value['map'], $match);
            }

            // 规则路由
            if (!empty($value['rule'])) {
                return $this->ruleRoute($value['rule'], $match);
            }    
        }

        return false;
    }

    /**
     * 获取当前请求的URI
     * @return string
     */
    public function getRequestUri() : string
    {
        $requestUri = str_replace($_SERVER['SCRIPT_NAME'], '', $_SERVER['REQUEST_URI']);
        
        if (!empty($_SERVER['QUERY_STRING'])) {
            $requestUri = str_replace('?'.$_SERVER['QUERY_STRING'], '', $_SERVER['REQUEST_URI']);
        }

        return $requestUri;
    }

    /**
     * @param $map
     * @param $match
     * @return bool
     * @throws ErrorException
     * @throws HttpException
     */
    private function mapRoute($map, array $match)
    {
        array_shift($match);
        $params = $match;

        if (!preg_match('/@/', $map)) {
            throw new ErrorException('路由定义错误，缺少@符号');
        }

        list($controller, $action) = explode('@', $map);
        $controller = $this->namespace . $controller;
        return $this->createController($controller, $action, $params);
    }


    /**
     * @param $rule
     * @param array $match
     * @return mixed
     * @throws HttpException
     */
    private function ruleRoute($rule, array $match)
    {
        // 解析出命名空间
        $position = strpos($rule,'\\');
        $namespace = empty($position) ? '' : substr($rule, 0, $position+1);
        $namespace = $this->namespace.$namespace;

        // 解析出控制器
        preg_match_all('/(\\{[A-Za-z]+\\})/', $rule, $controller);

        $controller = array_shift($controller);
        array_shift($match);

        $map = array_combine($controller, $match);
        $controller = !isset($map['{controller}']) ? '' : ucfirst($map['{controller}']);
        $controller = $namespace . $controller.'Controller';
        $action = !isset($map['{action}']) ? 'index' : $map['{action}'];
        unset($map['{controller}'], $map['{action}']);
        $params = array_values($map);

        return $this->createController($controller, $action, $params);
    }

    /**
     * 实例化一个Controller
     *
     * @param $controller
     * @param $action
     * @param $params
     * @return mixed
     * @throws HttpException
     */
    private function createController($controller, $action, $params)
    {
        if (!class_exists($controller)) {
            throw new HttpException(404, 'Class not found '.$controller);
        }

        if (!method_exists($controller, $action)) {
            throw new HttpException(404, 'Call to undefined method '.$controller.'::'.$action.'()');
        }

        $response = call_user_func_array([new $controller, $action], $params);
        return new Response($response);
    }

    /**
     * @param array $methods
     * @return bool
     * @throws HttpException
     */
    private function allowMethod(array $methods)
    {
        if (in_array($_SERVER['REQUEST_METHOD'], $methods)) {
            return true;
        }

        throw new HttpException(403, 'method '.$_SERVER['REQUEST_METHOD'].' not allow access');
    }
}
