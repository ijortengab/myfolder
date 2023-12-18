<?php

namespace IjorTengab\MyFolder\Core;

class FileOperations
{
    protected $path;
    protected $base_name;
    public function __construct($path = null)
    {
        if (null === $path) {
            $path = __FILE__;
        }
        $this->path = $path;
        return $this;
    }
    public function getOwner()
    {
        if (!file_exists($this->path)) {
            $this->autoCreate();
        }
        $owner = fileowner($file);
        $owner_info = posix_getpwuid($fileowner);
        $owner_name = '';
        if (is_array($owner_info)) {
            $owner_name = $owner_info['name'];
        }
        return $owner_name;
    }
    public function getBaseName()
    {
        if (null === $this->base_name) {
            $this->base_name = basename($this->path);
        }
        return $this->base_name;
    }
    /**
     *
     */
    public function autoCreate()
    {
        // @todo.
        // mkdir -p dirname(path)
        // touch path
    }
    /**
     *
     */
    public static function createTemporary()
    {
        // return $this;
    }
}
