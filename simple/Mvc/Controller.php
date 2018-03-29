<?php

namespace Simple\Mvc;

use App\Model\User;
use Simple\Support\Crypt;

abstract class Controller
{
    public static function isLogin(&$user)
    {
        if (empty($_SERVER['HTTP_AUTH']) || $_SERVER['HTTP_AUTH'] == 'null') {
            $user = [];
            return false;
        }
        $auth = $_SERVER['HTTP_AUTH'];
        $userId = Crypt::decrypt($auth);
        $userModel = new User();
        $user = $userModel->find($userId);
        if (empty($user)) {
            $user = [];
            return false;
        }

        return true;
    }

    public static function input()
    {
        $input = file_get_contents('php://input');
        $input = json_decode($input, true);

        return $input;
    }

    /**
     * 输出一个视图
     *
     * @param $file
     * @param array $data
     * @return bool|mixed
     */
    public static function view($file, array $data = [])
    {
        extract($data);
        $file = __DIR__ .'/../../app/View'.$file.'.php';
        return require "{$file}";
    }

    /**
     * 返回正确的json
     *
     * @param null $data
     * @return array
     */
    public static function success($data = null)
    {
        $data = empty($data) ? [] : $data;

        return [
            'httpCode' => 200,
            'status'=> 200,
            'code' => 0,
            'message' => 'ok',
            'data' => $data,
            'responseTime' => microtime(true) - START,
            'timestamp' => microtime(true)
        ];
    }

    /**
     * 返回错误的json
     *
     * @param int $code
     * @param string $message
     * @return array
     */
    public static function error($code = 0, $message = 'error')
    {
        return [
            'httpCode' => 200,
            'code' => $code,
            'message' => $message,
            'responseTime' => microtime(true) - START,
            'timestamp' => microtime(true)
        ];
    }
}
