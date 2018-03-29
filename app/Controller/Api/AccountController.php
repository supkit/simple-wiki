<?php

namespace App\Controller\Api;

use App\Model\User;
use Simple\Mvc\Controller;
use Simple\Support\Crypt;

class AccountController extends Controller
{
    /**
     * @return array
     * @throws \ErrorException
     */
    public function login()
    {
        $input = self::input();

        $email = $input['account'];
        $password = md5($input['password']);

        if (!preg_match('/[a-z0-9]+@[a-z0-9]+\.[a-z0-9]+/', $email)) {
            return self::error(101, '邮箱格式错误');
        }

        if (empty($password)) {
            return self::error(101, '密码不能为空');
        }

        $user = new User();
        $data = $user->where('email', '=', $email)->where('password', '=', $password)->select()->fetch();

        if (empty($data)) {
            return self::error(102, '邮箱或者密码错误');
        }

        if ($data['status'] == 0) {
            return self::error(103, '该用户尚未激活，请联系管理员');
        }

        $userId = $data['id'];
        $userId = Crypt::encrypt($userId);
        return self::success(['token' => $userId]);
    }

    /**
     * @return array
     * @throws \ErrorException
     */
    public function register()
    {
        $input = self::input();

        $email = $input['account'];
        $username = $input['username'];
        $password = md5($input['password']);
        $user = new User();
        $isset = $user->where('email', '=', $email)->select(['email'])->fetch();

        if (!empty($isset)) {
            return self::error(104, '该用户已经注册过了');
        }

        if (!preg_match('/[a-z0-9]+@[a-z0-9]+\.[a-z0-9]+/', $email)) {
            return self::error(101, '邮箱格式错误');
        }

        $insert = [
            'email' => $email,
            'username' => $username,
            'password' => $password
        ];
        $user->insert($insert);

        return self::success();
    }
}
