<?php

namespace IjorTengab\MyFolder\Core;

class ConfigEditor
{
    protected $doc_comment;
    protected $serialized_string;
    protected $file_name;
    protected $object;
    protected $class;
    protected $namespace;
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
        if (null === $this->serialized_string) {
            $this->serialize();
        }
        return $this->serialized_string;
    }
    protected function serialize()
    {
        $this->serialized_string = '';
        if (isset($this->object)) {
            $reflective_class = new \ReflectionClass($this->object); //construct the Reflective class.
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
            $reflective_class = new \ReflectionClass($class_name); //construct the Reflective class.
        }
        // Turn false return into empty string.
        $this->doc_comment = (string) $reflective_class->getDocComment();
        $this->file_name = $reflective_class->getFileName();
        $this->serialized_string = trim(preg_replace(array(
            '/^\/\*\*$/m',
            '/^\s\*\s/m',
            '/^\s\*\/$/m',
        ), '', $this->doc_comment));
    }
    public function set($data)
    {
        if (null === $this->serialized_string) {
            $this->serialize();
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
    }

    /**
     *
     */
    public static function toDocComment($string)
    {
        $string = preg_replace('/^/m',' * ', $string);
        return <<<EOF
/**
$string
 */
EOF;
    }

}
