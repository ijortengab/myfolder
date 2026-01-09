<?php

namespace IjorTengab\MyFolder\Core;

class ConfigEditor
{
    protected $doc_comment;
    protected $config_contents;
    protected $file_name;
    protected $object;
    protected $class;
    protected $namespace;

    public static function toDocComment($string)
    {
        $string = preg_replace('/^/m',' * ', $string);
        return <<<EOF
/**
$string
 */
EOF;
    }
    public function __construct($object = null)
    {
        if ($object !== null) {
            $this->object = $object;
        }
    }
    public function setClassName($class, $namespace = null)
    {
        $this->namespace = $namespace;
        $this->class = $class;
    }
    public function get()
    {
        if (null === $this->config_contents) {
            $this->populateConfigContents();
        }
        return $this->config_contents;
    }
    public function set($data)
    {
        if (null === $this->config_contents) {
            $this->populateConfigContents();
        }
        if (!is_writable(dirname($this->file_name))) {
            throw new WriteException('Directory of file is not writable.');
        }

        // Turn object into string and convert as doc comment.
        $data = $this->toDocComment((string) $data);

        $contents = file_get_contents($this->file_name);
        $contents = str_replace($this->doc_comment, $data, $contents);
        $temp_file = tempnam(sys_get_temp_dir(), 'MyFolder');
        file_put_contents($temp_file, $contents);

        $oldgroup = filegroup($this->file_name);
        rename($temp_file, $this->file_name);

        // Bring back the group, so we still editable this code.
        chmod($this->file_name, 0664);
        chgrp($this->file_name, $oldgroup);

        // Penting. Wajib invalidate atau jika tidak, maka ReflectionClass
        // akan mengembalikan doc comment versi cached pada simultan request.
        opcache_invalidate($this->file_name);
    }
    protected function populateConfigContents()
    {
        $this->config_contents = '';
        if (isset($this->object)) {
            //construct the Reflective class.
            $reflective_class = new \ReflectionClass($this->object);
        }
        else {
            if (null === $this->namespace) {
                $class_name = $this->class;
            }
            else {
                $class_name = "{$this->namespace}\\{$this->class}";
            }
            if (!class_exists($class_name)) {
                throw new WriteException('Class to get the configuration is not exists.');
            }
            //construct the Reflective class.
            $reflective_class = new \ReflectionClass($class_name);
        }
        // Turn false return into empty string.
        $this->doc_comment = (string) $reflective_class->getDocComment();
        $this->file_name = $reflective_class->getFileName();
        $this->config_contents = trim(preg_replace(array(
            '/^\/\*\*$/m',
            '/^\s\*\s/m',
            '/^\s\*\/$/m',
        ), '', $this->doc_comment));
    }
}
