<?php

namespace IjorTengab\MyFolder\Tools;

class ScanDirPhpScript implements \Iterator
{
    protected $psr4_namespace_prefix;

    protected $psr4_namespace_prefix_quoted;

    protected $psr4_base_directory;

    protected $excluded = array();

    protected $excluded_expanded = array();

    protected $namespace;

    protected $original_path;

    protected $path;

    protected $glob_iterator;

    /**
     *
     */
    public function __construct($psr4_namespace_prefix, $psr4_base_directory)
    {
        if (substr($psr4_base_directory, 0, 1) != '/') {
            $psr4_base_directory = getcwd().'/'.$psr4_base_directory;
        }
        // Make sure slash and backslash.
        $psr4_namespace_prefix = rtrim($psr4_namespace_prefix, '\\') . '\\';
        $psr4_base_directory = rtrim($psr4_base_directory, '/') . '/';
        $this->psr4_namespace_prefix = $psr4_namespace_prefix;
        $this->psr4_namespace_prefix_quoted = preg_quote($psr4_namespace_prefix);
        $this->psr4_base_directory = $psr4_base_directory;
    }

    /**
     *
     */
    public function setNameSpace($string)
    {
        $this->namespace = $string;
        $result = preg_replace('/^'.$this->psr4_namespace_prefix_quoted.'/','',$string);
        $result = str_replace('\\','/',$result);
        $this->path = $this->psr4_base_directory.$result;
        $this->reset();
        return $this;
    }

    /**
     *
     */
    public function exclude($filename)
    {
        $this->excluded[] = $filename;
    }

    /**
     *
     */
    public function getExcluded()
    {
        return $this->excluded;
    }

    /**
     *
     */
    public function getExcludedExpanded()
    {
        return $this->excluded_expanded;
    }

    /**
     *
     */
    protected function reset()
    {
        $this->excluded = array();
        $this->excluded_expanded = array();
        $this->glob_iterator = null;
    }
    public function current()
    {
        if ($this->valid()) {
            return $this->glob_iterator->current();
        }
    }
    public function key()
    {
        return $this->glob_iterator->key();
    }
    public function next()
    {
        $this->glob_iterator->next();
        $spl_file_info = $this->current();
        if (null === $spl_file_info) {
            return;
        }
        $filename = $spl_file_info->getFilename();
        if (in_array($filename, $this->excluded)) {
            $this->excluded_expanded[] = $spl_file_info;
            $this->next();
        }
    }
    public function rewind()
    {
        if (null === $this->glob_iterator) {
            $this->glob_iterator = new \GlobIterator($this->path.'/*.php');
        }
        if ($this->glob_iterator->valid() === false) {
            return;
        }
        $this->glob_iterator->rewind();
        $spl_file_info = $this->current();
        $filename = $spl_file_info->getFilename();
        if (in_array($filename, $this->excluded)) {
            $this->excluded_expanded[] = $spl_file_info;
            $this->next();
        }
        return $this;
    }
    public function valid()
    {
        return $this->glob_iterator->valid();
    }
}
