<?php

namespace IjorTengab\MyFolder\Core;

/**
 * # Example 1. Start from nothing.
 *
 * use IjorTengab\MyFolder\Core\ArrayHelper;
 *
 * $a = new ArrayHelper;
 * $a->site->setting->offline = true;
 *
 * var_export($a->dump());
 *
 * # Output:
 * #
 * # array (
 * #     'site' => array (
 * #         'setting' => array (
 * #             'offline' => true,
 * #          ),
 * #     ),
 * # )
 * #
 *
 * # Example 2. Walking
 *
 * use IjorTengab\MyFolder\Core\ArrayHelper;
 *
 * $array = array(
 *     'modules' => array(
 *         'enable' => array(
 *             'index',
 *             'user',
 *             'terminal',
 *         ),
 *         'disable' => array(
 *             'offline_mode',
 *             'markdown',
 *             'sysadmin',
 *         ),
 *     ),
 * );
 *
 * $a = new ArrayHelper($array);
 *
 * # Walking to key 'modules'.
 * $a->modules;
 *
 * # Do other process.
 * # ...
 *
 * # Walking to key 'enable'.
 * $a->enable;
 *
 * # Do other process.
 * # ...
 *
 * # Get value.
 *
 * $list = $a->value();
 *
 * # Get the same results but another way.
 *
 * $list = $a->reset()->modules->enable->value();
 *
 * var_export($list);
 *
 * # Output:
 *
 * # array (
 * #   0 => 'index',
 * #   1 => 'user',
 * #   2 => 'terminal',
 * # )
 */
class ArrayHelper
{
    protected $array_storage = array();
    protected $shortcut;
    protected $current_value;
    public function __construct($array = null)
    {
        if ($array !== null) {
            if (is_array($array)) {
                $this->array_storage = (array) $array;
            }
            else {
                throw new \RuntimeException('Argument 1 passed to ' . __method__. ' must be of the type array or null.');
            }
        }
    }
    public function __get($key)
    {
        return $this->get($key);
    }
    public function __set($key, $value)
    {
        if (null === $this->shortcut) {
            $this->shortcut = &$this->array_storage;
        }
        if (is_array($this->shortcut)) {
            $this->shortcut[$key] = $value;
        }
        else {
            // Jika bukan array, maka terpaksa kita destroy value.
            $this->shortcut = array($key => $value);
        }
        // Reset alias.
        $this->reset();
    }
    public function value()
    {
        // Reset alias.
        $this->shortcut = &$this->array_storage;
        return $this->current_value;
    }
    public function index(int $int)
    {
        return $this->get($int);
    }
    public function append($value, $is_continue = false)
    {
        if (null === $this->shortcut) {
            $this->shortcut = &$this->array_storage;
        }
        $this->shortcut[] = $value;
        // Refresh value.
        $this->current_value = $this->shortcut;
        if ($is_continue) {
            return $this;
        }
        $this->reset();
    }
    public function prepend($value, $is_continue = false)
    {
        if (null === $this->shortcut) {
            $this->shortcut = &$this->array_storage;
        }
        array_unshift($this->shortcut, $value);
        // Refresh value.
        $this->current_value = $this->shortcut;
        if ($is_continue) {
            return $this;
        }
        $this->reset();
    }
    public function reset()
    {
        // Reset alias.
        $this->shortcut = &$this->array_storage;
        $this->current_value = null;
        return $this;
    }
    public function addBefore($key, $newKey, $value, $is_continue = false)
    {
        $array = &$this->shortcut;
        $keys = array_keys($array);
        $position = array_search($key, $keys);
        $array = array_merge(array_slice($array, 0, $position), array($newKey => $value), array_slice($array, $position));
        if ($is_continue) {
            return $this;
        }
        $this->reset();
    }
    public function addBeforeIndex(int $index, $value, $is_continue = false)
    {
        $array = &$this->shortcut;
        $keys = array_keys($array);
        $position = array_search($index, $keys);
        $i = array_search($index, $keys);
        $position = $i === false ? count($array) : $i;
        $array = array_merge(array_slice($array, 0, $position), array($value), array_slice($array, $position));
        if ($is_continue) {
            return $this;
        }
        $this->reset();
    }
    public function addAfter($key, $newKey, $value, $is_continue = false)
    {
        $array = &$this->shortcut;
        $keys = array_keys($array);
        $position = array_search($key, $keys) + 1;
        $array = array_merge(array_slice($array, 0, $position), array($newKey => $value), array_slice($array, $position));
        if ($is_continue) {
            return $this;
        }
        $this->reset();
    }
    public function addAfterIndex(int $index, $value, $is_continue = false)
    {
        $array = &$this->shortcut;
        $keys = array_keys($array);
        $i = array_search($index, $keys);
        $position = $i === false ? count($array) : $i + 1;
        $array = array_merge(array_slice($array, 0, $position), array($value), array_slice($array, $position));
        if ($is_continue) {
            return $this;
        }
        $this->reset();
    }
    public function dump()
    {
        $this->reset();
        return $this->array_storage;
    }
    public function restore(array $array)
    {
        $this->array_storage = $array;
        $this->reset();
        return $this;
    }
    protected function get($key)
    {
        if (null === $this->shortcut) {
            // Buat alias.
            $this->shortcut = &$this->array_storage;
        }
        if (is_array($this->shortcut)) {
            // Jika array, tapi.
            if (array_key_exists($key, $this->shortcut)) {
                // Ubah alias.
                $this->shortcut = &$this->shortcut[$key];
                // Set value.
                $this->current_value = $this->shortcut;
            }
            else {
                $this->shortcut[$key] = array();
                // Ubah alias.
                $this->shortcut = &$this->shortcut[$key];
                // Set value.
                $this->current_value = null;
            }
        }
        else {
            // Jika bukan array, maka terpaksa kita destroy value.
            $this->shortcut = array($key => array());
            // Ubah alias.
            $this->shortcut = &$this->shortcut[$key];
            // Set value.
            $this->current_value = null;
        }
        return $this;
    }
}
