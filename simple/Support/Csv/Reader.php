<?php

namespace Simple\Support\Csv;

use SplFileObject;

class Reader
{
    /**
     * @var string
     */
    protected $file = '';

    /**
     * @var null | SplFileObject
     */
    protected $fileObject = null;

    /**
     * Reader constructor.
     * @param $file
     */
    public function __construct($file)
    {
        if (file_exists($file)) {
            $this->file = $file;
        }
    }

    /**
     * 新建一个FileObject
     */
    public function openFile()
    {
        if ($this->fileObject == null) {
            $this->fileObject = new SplFileObject($this->file, 'rb');
        }
    }

    /**
     * @return int
     */
    public function getLine()
    {
        $this->openFile();
        $this->fileObject->seek(filesize($this->file));
        return $this->fileObject->key();
    }

    /**
     * @param int $length
     * @param int $start
     * @return array
     */
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
