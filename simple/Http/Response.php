<?php

namespace Simple\Http;

class Response
{
    /**
     * Response constructor.
     * @param $response
     */
    public function __construct($response)
    {
        return $this->invoke($response);
    }

    /**
     * http response
     *
     * @param $data
     * @return bool
     */
    private function invoke($data)
    {
        if (is_string($data)) {
            echo $data;
            return true;
        }

        if (is_array($data) || is_object($data)) {
            header("Content-Type: application/json; charset=utf-8");
            echo json_encode($data, true);
            return true;
        }

        return false;
    }
}
