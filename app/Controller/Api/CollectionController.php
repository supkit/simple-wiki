<?php

namespace App\Controller\Api;

use App\Model\Collection;
use App\Model\User;
use Simple\Mvc\Controller;

class CollectionController extends Controller
{
    /**
     * @return array
     * @throws \ErrorException
     */
    public function item()
    {
        $data['isLogin'] = self::isLogin($user);
        $collection = new Collection();
        $list = $collection->order(['id' => 'desc'])->select()->fetchAll();

        $data['public'] = [];
        $data['private'] = [];

        foreach ($list as $key => $value) {
            if ($value['public'] == 0) {
                array_push($data['private'], $value);
            } else {
                array_push($data['public'], $value);
            }
        }

        $data['user'] = $user;
        return self::success($data);
    }

    /**
     * @return array
     * @throws \ErrorException
     */
    public function create()
    {
        self::isLogin($user);

        if ($user['isSuper'] == 0) {
            return self::error(101, '抱歉，您没有添加项目的权限');
        }

        $input = self::input();

        $insert['member'] = empty($input['member']) ? '[]' : $input['member'];
        $insert['name'] = $input['name'];
        $insert['public'] = (int) $input['public'];
        $insert['description'] = $input['description'];
        $insert['created'] = time();
        $insert['updated'] = time();

        $collection = new Collection();
        $id = $collection->insert($insert, true);

        return self::success(['id' => $id]);
    }

    /**
     * @return array
     * @throws \ErrorException
     */
    public function update()
    {
        self::isLogin($user);

        if (empty($user) || $user['isSuper'] == 0) {
            return self::error(101, '抱歉，您没有修改项目的权限');
        }

        $input = self::input();

        $collection = new Collection();
        $id = $input['id'];
        $update = [
            'name' => $input['name'],
            'description' => $input['description'],
            'public' => $input['public'],
            'member' => json_encode($input['member'])
        ];

        $collection->where('id', '=', $id)->update($update);

        return self::success();
    }

    /**
     * @param $id
     * @return array
     * @throws \ErrorException
     */
    public function data($id)
    {
        self::isLogin($user);

        if (empty($user) || $user['isSuper'] == 0) {
            return self::error(101, '抱歉，您没有修改项目的权限');
        }

        $collection = new Collection();
        $detail = $collection->find($id);

        $userModel = new User();
        $user = $userModel->where('status', '=', 1)->select(['id', 'username'])->fetchAll();

        $data['detail'] = $detail;
        $data['userAll'] = $user;

        return self::success($data);
    }
}