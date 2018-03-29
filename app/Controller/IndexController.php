<?php

namespace App\Controller;

use Simple\Mvc\Controller;

class IndexController extends Controller
{
    public function index()
    {
        return self::success(['author', 'chenshuo', 'chenshuo.net']);
    }
}
