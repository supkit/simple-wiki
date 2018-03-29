<?php

namespace Simple\Http;

class Input
{
    private $contentType = [
        'application/json'
    ];

    private static $get = [];

    private static $post = [];

    private static $instance = null;

    public function __construct()
    {
        self::$post = $_POST;
        self::$get = $_GET;
    }

    public static function instance()
    {
        if (empty(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public static function get($key = null, $default = '')
    {
        self::instance();

        if (empty($key)) {
            return self::$get;
        }

        if (!empty(self::$get[$key])) {
            return self::$get[$key];
        }

        return $default;
    }

    public static function post($key = null, $default = '')
    {
        self::instance();

        if (empty($key)) {
            return self::$post;
        }

        if (!empty(self::$post[$key])) {
            return self::$post[$key];
        }

        return $default;
    }

    public static function put()
    {

    }

    public static function delete()
    {

    }

    public static function any()
    {

    }

    public static function file($key)
    {
        return $_FILES[$key];
    }
}
