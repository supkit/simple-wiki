<?php

namespace Simple\Support;

use SplFileObject;

class CsvReader
{
    protected $file = '';

    /**
     * @var null | SplFileObject
     */
    protected $fileObject = null;

    public function __construct($file)
    {
        if (file_exists($file)) {
            $this->file = $file;
        }
    }

    public function openFile()
    {
        if ($this->fileObject == null) {
            $this->fileObject = new SplFileObject($this->file, 'rb');
        }
    }

    public function getLine()
    {
        $this->openFile();
        $this->fileObject->seek(filesize($this->file));
        return $this->fileObject->key();
    }

    public function getData($length = 0, $start = 0)
    {
        $this->openFile();

        $length = $length ? $length : $this->getLine();

        $data = [];

        $this->fileObject->seek($start);

        while ($length-- && !$this->fileObject->eof()) {
            $data[] = $this->fileObject->fgetcsv();
            $this->fileObject->next();
        }

        return $data;
    }
}
