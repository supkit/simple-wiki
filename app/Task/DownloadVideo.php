<?php

namespace App\Task;

class DownloadVideo
{
    public $data = [];

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function handle()
    {
        var_export($this->data);
        $url = $this->data['url'];

        $shell = 'you-get {$url}';

        echo shell_exec($shell);
    }
}