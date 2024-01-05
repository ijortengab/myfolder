<?php

namespace IjorTengab\MyFolder\Core;

class Config
{
    public static $_dump_lines = array();
    protected static $_dump_key_storage = array();
    protected static $_dump_is_indexed_array = false;
    protected static $_dump_is_indexed_array_sorted = false;
    protected $shortcut;
    protected $editor;
    protected $array_storage = array();
    protected $current_storage;
    protected $dump_key_storage = array();
    protected $dump_lines = array();
    protected $dump_is_indexed_array = false;
    protected $dump_is_indexed_array_sorted = false;
    protected $is_null = false;
    private $clear;

    public static function flattenArray($array, $wrap_indexed_array = true)
    {
        foreach ($array as $key => $value) {
            self::$_dump_key_storage[] = $key;
            if (is_array($value)) {
                if (self::_isIndexedArray($value) && $wrap_indexed_array) {
                    $last = array_pop(self::$_dump_key_storage);
                    $new_array = array();
                    $new_indexed_array_sorted = array();
                    foreach ($value as $key2 => $value2) {
                        if (self::$_dump_is_indexed_array_sorted) {
                            $new_indexed_array_sorted[] = $value2;
                        }
                        else {
                            $new_array["$last".'['."$key2".']'] = $value2;
                        }
                    }
                    if ($new_array) {
                        self::flattenArray($new_array, $wrap_indexed_array);
                    }
                    if ($new_indexed_array_sorted) {
                        while ($value3 = array_shift($new_indexed_array_sorted)) {
                            $new_array = array();
                            $new_array["$last".'[]'] = $value3;
                            self::flattenArray($new_array, $wrap_indexed_array);
                        }
                    }
                }
                else {
                    self::flattenArray($value, $wrap_indexed_array);
                }
            }
            else {
                self::$_dump_lines[] = array(
                    implode('.', self::$_dump_key_storage),
                    $value,
                );
            }
            array_pop(self::$_dump_key_storage);
        }
        return self::$_dump_lines;

    }
    public static function load($module = null)
    {
        if (null === $module) {
            $editor = new ConfigEditor;
            $editor->setClassName('ConfigStorage', 'IjorTengab\MyFolder\Core');
            $config = new self;
            $config->setEditor($editor);
            $config->parse($editor->get());
            return $config;
        }
        else {
            $class = str_replace(' ', '', ucwords(str_replace('_', ' ', $module)));
            $editor = new ConfigEditor;
            $editor->setClassName('Config', 'IjorTengab\\MyFolder\\Module\\'.$class);
            $config = new self;
            $config->setEditor($editor);
            $config->parse($editor->get());
            return $config;
        }
    }
    public static function save(Config $config)
    {
        // Bring back editor.
        $editor = $config->getEditor();
        $editor->set($config);
    }
    public function __construct($shortcut = false)
    {
        $this->shortcut = $shortcut;
    }
    public function enableShortcut()
    {
        $this->shortcut = true;
    }
    public function setEditor(ConfigEditor $editor)
    {
        $this->editor = $editor;
    }
    public function getEditor()
    {
        return $this->editor;
    }
    public function __set($a, $b)
    {
        if (null === $this->current_storage) {
            $this->current_storage = &$this->array_storage;
        }
        if ($this->shortcut) {
            if (str_ends_with($a, '__')) {
                $a = substr($a, 0, -2).'[]';
            }
            elseif (str_ends_with($a, '_')) {
                if (preg_match('/^(.*)_(\d+)_$/',$a, $m)) {
                    $a = $m[1];
                    $i = $m[2];
                    $replacement = '['.$i.']';
                    $replacement_length = strlen($replacement);
                    $a = substr($a, 0, -$replacement_length).$replacement;
                };
            }
        }
        if (str_ends_with($a, '[]')) {
            // Berarti append.
            $a = substr($a, 0, -2);
            $this->current_storage[$a][] = $b;
        }
        elseif (str_ends_with($a, ']')) {
            // Kemungkinan fill indexed array.
            if (preg_match('/^(.*)\[(\d+)\]$/',$a, $m)) {
                $a = $m[1];
                $i = $m[2];
                $this->current_storage[$a][$i] = $b;
            };
        }
        else {
            if (!array_key_exists($a, $this->current_storage)) {
                $this->current_storage[$a] = array();
            }
            $this->current_storage[$a] = $b;
        }
        $this->current_storage = &$this->clear;
    }
    public function __get($a)
    {
        if (null === $this->current_storage) {
            $this->current_storage = &$this->array_storage;
        }
        if ($this->shortcut) {
            if (str_ends_with($a, '_')) {
                if (preg_match('/^(.*)_(\d+)_$/',$a, $m)) {
                    $a = $m[1];
                    $i = $m[2];
                    $replacement = '['.$i.']';
                    $replacement_length = strlen($replacement);
                    $a = substr($a, 0, -$replacement_length).$replacement;
                };
            }
        }
        if (str_ends_with($a, ']')) {
            // Kemungkinan fill indexed array.
            if (preg_match('/^(.*)\[(\d+)\]$/',$a, $m)) {
                $a = $m[1];
                $i = $m[2];
                if (!array_key_exists($a, $this->current_storage)) {
                    $this->current_storage[$a] = array();
                }
                if (!array_key_exists($i, $this->current_storage[$a])) {
                    $this->current_storage[$a][$i] = array();
                }
                $this->current_storage = &$this->current_storage[$a][$i];
            };
        }
        else {
            if (!array_key_exists($a, $this->current_storage)) {
                $this->current_storage[$a] = array();
            }
            $this->current_storage = &$this->current_storage[$a];
        }
        return $this;
    }
    public function __toString()
    {
        $current = $this->current_storage;
        $this->current_storage = &$this->clear;
        if (is_string($current)) {
            return $current;
        }
        else {
            $this->is_null = true;
        }
        if (null === $current) {
            $array = $this->array_storage;
            $this->dumpArray($array);
            return implode(PHP_EOL, $this->dump_lines);
        }
        return '';
    }
    public function value()
    {
        $value = (string) $this;
        if ($this->is_null) {
            return null;
        }
        return $value;
    }
    public function list()
    {
        $current = $this->current_storage;
        $this->current_storage = &$this->clear;
        if (is_array($current)) {
            return $current;
        }
        return array();
    }
    public function parse($string)
    {
        $lines = explode("\n", $string);
        foreach ($lines as $line) {
            $line = trim($line);
            if (str_starts_with($line, '.')) {
                $line = substr($line, 1);
                $segment = explode(' ', $line);
                if (count($segment) == 2) {
                    $address = $segment[0];
                    $value = $segment[1];
                    $keys = explode('.', $address);
                    $last = array_pop($keys);
                    foreach ($keys as $key) {
                        $this->$key;
                    }
                    $this->$last = $value;
                }
            }
        }
    }
    protected static function _isIndexedArray($array)
    {
        $keys = array_keys($array);
        $filtered = array_filter($keys, 'is_numeric');
        $return = array_diff($keys, $filtered);
        if (empty($return)) {
            $i = 0;
            do {
                $aa = current($keys);
                $bb = $i;
                if (current($keys) === $i++) {
                    next($keys);
                    if (current($keys) === false) {
                        break;
                    }
                    self::$_dump_is_indexed_array_sorted = true;
                    continue;
                }
                else{
                    self::$_dump_is_indexed_array_sorted = false;
                    break;
                }
            }
            while (true);
            self::$_dump_is_indexed_array = true;
        }
        else {
            self::$_dump_is_indexed_array = false;
        }
        return self::$_dump_is_indexed_array;
    }
    protected function isIndexedArray($array)
    {
        $keys = array_keys($array);
        $filtered = array_filter($keys, 'is_numeric');
        $return = array_diff($keys, $filtered);
        if (empty($return)) {
            $i = 0;
            do {
                $aa = current($keys);
                $bb = $i;
                if (current($keys) === $i++) {
                    next($keys);
                    if (current($keys) === false) {
                        break;
                    }
                    $this->dump_is_indexed_array_sorted = true;
                    continue;
                }
                else{
                    $this->dump_is_indexed_array_sorted = false;
                    break;
                }
            }
            while (true);
            $this->dump_is_indexed_array = true;
        }
        else {
            $this->dump_is_indexed_array = false;
        }
        return $this->dump_is_indexed_array;
    }
    protected function dumpArray($array)
    {
        // @todo gunakan flattenArray.
        // @todo parse method agar bisa menhandle spasi pada value. jangan pakai explode.
        foreach ($array as $key => $value) {
            $this->dump_key_storage[] = $key;
            if (is_array($value)) {
                if ($this->isIndexedArray($value)) {
                    $last = array_pop($this->dump_key_storage);
                    $new_array = array();
                    $new_indexed_array_sorted = array();
                    foreach ($value as $key2 => $value2) {
                        if ($this->dump_is_indexed_array_sorted) {
                            $new_indexed_array_sorted[] = $value2;
                        }
                        else {
                            $new_array["$last".'['."$key2".']'] = $value2;
                        }
                    }
                    if ($new_array) {
                        $this->dumpArray($new_array);
                    }
                    if ($new_indexed_array_sorted) {
                        while ($value3 = array_shift($new_indexed_array_sorted)) {
                            $new_array = array();
                            $new_array["$last".'[]'] = $value3;
                            $this->dumpArray($new_array);
                        }
                    }
                }
                else {
                    $this->dumpArray($value);
                }
            }
            else {
                $this->dump_lines[] = '.'.implode('.', $this->dump_key_storage).' '.$value;
            }
            array_pop($this->dump_key_storage);
        }
    }
}
