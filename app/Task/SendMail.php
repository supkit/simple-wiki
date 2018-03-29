<?php

namespace App\Task;

class SendMail
{
    public $data = [];

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function handle()
    {
        $mail = $this->data['mail'];
        sleep(5);
        echo $mail;
    }
}
