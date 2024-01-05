<?php

namespace IjorTengab\MyFolder\Core;

class TwigFile
{
    protected $template;
    protected $html;
    protected $array_storage;
    protected $string_storage;
    protected $stringify_array_storage;

    public static function process($object, $array)
    {
        $twig = new self($object, $array);
        return $twig;
    }
    public function __construct($object, $array)
    {
        $this->string_storage = array();
        $this->array_storage = array();
        $this->template = $object;
        foreach ($array as $key => $value) {
            if (is_string($value)) {
                $this->string_storage[$key] = $value;
            }
            elseif (is_array($value)) {
                $this->array_storage[$key] = $value;
            }
        }
    }
    public function __toString()
    {
        $this->html = (string) $this->template;
        $this->resolveForTag();
        $this->resolveParameter();
        return $this->html;
    }
    public function resolveForTag()
    {
        $storage = $this->scanForTag();
        do {
            $scope = $this->html;
            $find = '{% endfor %}';
            $distance = stripos($scope, $find);
            $key = key($storage);
            $value = current($storage);
            if (next($storage)) {
                // Return false, if next element is not exists.
                $key_next = key($storage);
                if ($key_next < $distance) {
                    // Berarti nested: block didalam block.
                    continue;
                }
            }
            // Jika tidak ada lagi, maka variable $key akan bernilai NULL.
            if ($key === null) {
                break;
            }
            // Mulai mendapatkan keseluruhan block.
            $end = $distance + strlen($find);
            $offset = $key;
            $length = $end - $offset;
            $block = substr($this->html, $offset, $length);
            // Pecah-pecah deh.
            $string_before = substr($this->html, 0, $offset);
            $string_rendered = $this->renderForTag($block);
            $string_after = substr($this->html, $end);
            $string_after = $this->ltrim($string_after);
            $this->html = $string_before.$string_rendered.$string_after;
            // Rescan.
            $storage = $this->scanForTag();
        }
        while (count($storage));
    }
    protected static function ltrim($html)
    {
        if (isset($html[0]) && $html[0] === "\n") {
            $html = substr($html, 1);
        }
        return $html;
    }
    /**
     * @return array
     */
    protected function scanForTag()
    {
        // Set storage.
        $storage = array();
        preg_match_all('/{% for ([a-z]+) in ([a-z]+) %}/', $this->html, $matches, PREG_SET_ORDER);
        // Find string.
        $scope = $this->html;
        $offset = 0;
        while($each = array_shift($matches)) {
            list($find,,) = $each;
            $distance = stripos($scope, $find);
            // Masukkan ke storage.
            $position = $distance + $offset;
            $storage[$position] = $find;
            // Ubah scope pencarian.
            $offset += $distance + strlen($find);
            $scope = substr($this->html, $offset);
        }
        return $storage;
    }
    protected function renderForTag($html)
    {
        $database = $this->array_storage;
        $contents_empty = '';
        if (strpos($html, '{% else %}') !== false) {
            preg_match('/{% for ([a-z]+) in ([a-z]+) %}(.*){% else %}(.*)({% endfor %})/s', $html, $matches);
            list(,$parameter, $array, $contents, $contents_empty) = $matches;
            $contents_empty = $this->ltrim($contents_empty);
        }
        else {
            preg_match('/{% for ([a-z]+) in ([a-z]+) %}(.*)({% endfor %})/s', $html, $matches);
            list(,$parameter, $array, $contents) = $matches;
        }
        $contents = $this->ltrim($contents);
        $rendered = '';
        do {
            if (!array_key_exists($array, $database)) {
                break;
            }
            if (empty($database[$array])) {
                $rendered = $contents_empty;
                break;
            }
            foreach ($database[$array] as $value) {
                $rendered .= $this->renderParameters(array(
                    $parameter => $value,
                ), $contents);
            }
        }
        while (false);
        return $rendered;
    }
    protected function resolveParameter()
    {
        $this->html = $this->renderParameters();
    }
    protected function renderParameters($additional = array(), $contents = null)
    {
        if (null === $contents) {
            $contents = $this->html;
        }
        $database = $this->string_storage;
        $stringify = $this->stringifyArrayStorage();
        if (!empty($stringify)) {
            $database = array_replace_recursive($database, $stringify);
        }
        if (!empty($additional)) {
            $database = array_replace_recursive($database, $additional);
        }
        $new_database = array();
        array_walk($database, function ($value, $key) use (&$new_database) {
            $new_database['{{ '.$key.' }}'] = $value;
        });
        return strtr($contents, $new_database);
    }
    protected function stringifyArrayStorage()
    {
        if (null !== $this->stringify_array_storage) {
            return $this->stringify_array_storage;
        }
        $database = $this->array_storage;
        $records = Config::flattenArray($database, false);
        $new_database = array();
        while ($record = array_shift($records)) {
            list($key, $value) = $record;
            $new_database[$key] = $value;
        }
        $this->stringify_array_storage = $new_database;
        return $new_database;
    }
}
