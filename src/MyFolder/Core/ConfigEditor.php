<?php

namespace IjorTengab\MyFolder\Core;

class ConfigEditor
{
    protected $config_contents;
    protected $object;
    protected $class;
    protected $namespace;
    protected $reflection_class;

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
        $filename = $this->reflection_class->getFileName();
        if (!is_writable(dirname($filename))) {
            throw new WriteException('Directory of file is not writable.');
        }

        // Turn object into string and convert as doc comment.
        $data = $this->toDocComment((string) $data);

        $class_start_line =  $this->reflection_class->getStartLine();
        $doc_comment = $this->reflection_class->getDocComment();
        $additional_lines = 0;
        if ($doc_comment) {
            $count_chars = count_chars($doc_comment, 1);
            if (array_key_exists(10, $count_chars)) {
                $additional_lines = $count_chars[10] + 1;
            }
        }
        $lines = file($filename);
        if ($additional_lines) {
            $doc_comment_start_line = $class_start_line - $additional_lines;
            $doc_comment_end_line = $class_start_line - 1;
            array_splice($lines, --$doc_comment_start_line, $doc_comment_end_line - $doc_comment_start_line, $data.PHP_EOL);
        }
        else {
            // doc comment gak exists karena user menghapus manual.
            array_splice($lines, --$class_start_line, 0, $data.PHP_EOL);
        }
        $contents = implode('', $lines);
        $temp_file = tempnam(sys_get_temp_dir(), 'MyFolder');
        file_put_contents($temp_file, $contents);

        $oldgroup = filegroup($filename);
        rename($temp_file, $filename);

        // Bring back the group, so we still editable this code.
        chmod($filename, 0664);
        chgrp($filename, $oldgroup);

        // Penting. Wajib invalidate atau jika tidak, maka ReflectionClass
        // akan mengembalikan doc comment versi cached pada simultan request.
        opcache_invalidate($filename);
    }
    protected function populateConfigContents()
    {
        $this->config_contents = '';
        if (isset($this->object)) {
            //construct the Reflective class.
            $reflection_class = new \ReflectionClass($this->object);
            $this->reflection_class = $reflection_class;
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
            $reflection_class = new \ReflectionClass($class_name);
            $this->reflection_class = $reflection_class;
        }
        // Turn false return into empty string.
        $doc_comment = (string) $reflection_class->getDocComment();
        $this->config_contents = trim(preg_replace(array(
            '/^\/\*\*$/m',
            '/^\s\*\s/m',
            '/^\s\*\/$/m',
        ), '', $doc_comment));
    }
}
