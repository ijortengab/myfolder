<?php

namespace IjorTengab\MyFolder\Tools;

class PhpScript
{
    protected $filename;
    protected $chapter;

    /**
     *
     */
    public function __construct($filename)
    {
        $this->filename = $filename;
        $this->chapter = new Chapter;
    }

    /**
     *
     */
    public function setTotalChapter(int $int)
    {
        $this->chapter->setTotalChapter($int);
    }

    /**
     *
     */
    public function inChapter(int $int)
    {
        $this->chapter->inChapter($int);
        return $this;
    }

    /**
     *
     */
    public function appendThis($mixed)
    {
        if ($mixed instanceOf Chapter) {
            $mixed = (string) $mixed;
            // Ada enter karena array_walk.
            $mixed = substr($mixed, 0, -1);
        }
        $this->chapter->appendThis($mixed);
    }

    /**
     *
     */
    public function save()
    {
        $contents = (string) $this->chapter;
        // Ada enter karena array_walk.
        $contents = substr($contents, 0, -1);
        // Prepare directory.
        $dirname = dirname($this->filename);
        if (!is_dir($dirname)) {
            mkdir($dirname, 0755, true);
        }
        file_put_contents($this->filename, $contents);
    }
}
