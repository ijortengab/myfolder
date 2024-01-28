<?php

namespace IjorTengab\MyFolder\Core;

class JsonQuery
{
    protected $array_helper;
    protected $current_storage;
    protected $current_value;
    protected $last_key;

    public function __construct($array = null)
    {
        if ($array instanceOf ArrayHelper) {
            $this->array_helper = $array;
        }
        else {
            $this->array_helper = new ArrayHelper($array);
        }
    }

    public function path($path)
    {
        if (!str_starts_with($path, '.')) {
            throw new \RuntimeException('Argument 1 passed to ' . __method__. ' must be starting with dot character.');
        }
        $path = substr($path, 1);
        // Jika sebelumnya sudah perform ->add(), maka perlu kita reset.
        $this->array_helper->reset();
        $keys = explode('.', $path);
        // Duplicate dot bersamaan perlu kita hindari.
        $keys = array_filter($keys);
        $last_key = array_pop($keys);
        $this->last_key = $last_key;
        if (empty($keys)) {
            return $this;
        }
        do {
            $key = current($keys);
            $this->array_helper->{$key};
        }
        while (next($keys));
        return $this;
    }

    /**
     *
     */
    public function get()
    {
        $last_key = $this->last_key;
        $this->array_helper->$last_key;
        return $this->array_helper->value();
    }

    /**
     *
     */
    public function set($value)
    {
        if ($this->last_key === null) {
            // Harus run ->path() terlebih dahulu.
            trigger_error('Unable process '.__METHOD__.'.');
        }
        else {
            $last_key = $this->last_key;
            $this->array_helper->$last_key = $value;
            $this->last_key = null;
        }
    }

    /**
     *
     */
    public function add($value)
    {
        if ($this->last_key !== null) {
            $last_key = $this->last_key;
            $this->array_helper->$last_key;
            $this->last_key = null;
        }
        $this->array_helper->append($value, true);
        return $this;
    }

    /**
     *
     */
    public function export()
    {
        $records = JsonQueryHelper::serialize($this->array_helper->dump(), array(
            'indexed_array_append_format' => true,
        ));
        $contents = array();
        while ($record = array_shift($records)) {
            list($address, $value) = $record;
            $contents[] = ".{$address} {$value}";
        }
        return implode("\n", $contents);
    }

    /**
     *
     */
    public function import(string $string)
    {
        $lines = explode("\n", $string);
        foreach ($lines as $line) {
            $line = trim($line);
            if (str_starts_with($line, '.')) {
                $segment = explode(' ', $line);
                if (count($segment) > 1) {
                    $address = array_shift($segment);
                    $value = implode(' ', $segment);
                    // Cek substring.
                    if (\str_ends_with($address, '[]')) {
                        $address = substr($address, 0, -2);
                        $method = 'add';
                    }
                    else {
                        $method = 'set';
                    }
                    $this->path($address)->$method($value);
                }
            }
        }
        // Jika method terakhir dieksekusi adalah ::add, maka kita perlu
        // reset.
        $this->array_helper->reset();
    }
}
