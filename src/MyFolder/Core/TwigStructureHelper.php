<?php

namespace IjorTengab\MyFolder\Core;

class TwigStructureHelper
{
    protected $path;
    protected $key;
    protected $value;
    protected $parent;
    protected $is_tag_open = false;
    protected $is_tag_close = false;
    protected $is_tag_else = false;
    protected $tag_open_position;
    protected $tag_else_position;
    protected $tag_close_position;
    protected $block;
    protected $childrens = array();
    public function __construct($key, $value)
    {
        $this->key = $key;
        $this->value = $value;
        switch ($value) {
            case 'if':
                $this->is_tag_open = true;
                $this->block = 'if';
                $this->tag_open_position = $key;
                break;
            case 'for':
                $this->is_tag_open = true;
                $this->block = 'for';
                $this->tag_open_position = $key;
                break;
            case 'endfor':
                $this->is_tag_close = true;
                $this->block = 'for';
                break;
            case 'endif':
                $this->is_tag_close = true;
                $this->block = 'if';
                break;
            case 'else':
                $this->is_tag_else = true;
                break;
        }
        if ($this->block) {
            $this->path = '/'.$this->block;
        }
    }
    public function __toString()
    {
        return $this->block;
    }
    public function setAsCloseTag(TwigStructureHelper $object)
    {
        $this->tag_close_position = $object->getKey();
    }
    public function setAsElseTag(TwigStructureHelper $object)
    {
        $this->tag_else_position = $object->getKey();
    }
    public function setParent(TwigStructureHelper $object)
    {
        $this->parent = $object;
        // $debugname = 'object'; $debugvariable = '|||wakwaw|||'; if (array_key_exists($debugname, get_defined_vars())) { $debugvariable = $$debugname; } elseif (isset($this) && property_exists($this, $debugname)){ $debugvariable = $this->{$debugname}; $debugname = '$this->' . $debugname; } if ($debugvariable !== '|||wakwaw|||') {        echo "\r\n<pre>" . basename(__FILE__ ). ":" . __LINE__ . " (Time: " . date('c') . ", Direktori: " . dirname(__FILE__) . ")\r\n". 'var_dump(' . $debugname . '): '; var_dump($debugvariable); echo "</pre>\r\n"; }
        $value = (string) $object;
        // $debugname = 'parent'; $debugvariable = '|||wakwaw|||'; if (array_key_exists($debugname, get_defined_vars())) { $debugvariable = $$debugname; } elseif (isset($this) && property_exists($this, $debugname)){ $debugvariable = $this->{$debugname}; $debugname = '$this->' . $debugname; } if ($debugvariable !== '|||wakwaw|||') {        echo "\r\n<pre>" . basename(__FILE__ ). ":" . __LINE__ . " (Time: " . date('c') . ", Direktori: " . dirname(__FILE__) . ")\r\n". 'var_dump(' . $debugname . '): '; var_dump($debugvariable); echo "</pre>\r\n"; }
        $this->path = '/'.$value.$this->path;
        // die('oy');
        while ($parent = $object->getParent()){
            $value = (string) $parent;
            $this->path = '/'.$value.$this->path;
            $object = $parent;
        }
        
        
    }
    public function addChild(TwigStructureHelper $object)
    {
        $this->childrens[$object->getKey()] = $object;
    }
    public function getParent()
    {
        return $this->parent;
    }
    public function isTagOpen()
    {
        return $this->is_tag_open;
    }
    public function isTagClose()
    {
        return $this->is_tag_close;
    }
    public function isTagElse()
    {
        return $this->is_tag_else;
    }
    public function getKey()
    {
        return $this->key;
    }
    public function getPosition()
    {
        return array($this->tag_open_position,$this->tag_else_position,$this->tag_close_position);
    }
    public function getChildrens()
    {
        return $this->childrens;
    }
    public function getPath()
    {
        return $this->path;
    }
}
