<?php

namespace IjorTengab\MyFolder\Core;

class JsonQueryHelper
{
    protected static $last;
    protected static $key_storage = array();
    protected static $is_indexed_array = false;
    protected static $is_indexed_array_sorted = false;
    protected static $lines = array();

    public static function serialize($array, $options = array())
    {
        $options += array(
            // Set true jika digunakan untuk variable pada twig.
            'skip_indexed_array' => false,
            // Set true jika digunakan untuk dump config.
            'indexed_array_append_format' => false,
        );
        if ($options['skip_indexed_array'] && static::isIndexedArray($array)) {
            return;
        }
        foreach ($array as $key => $value) {
            if (\str_contains($key, '.')) {
                throw new \RunTimeException('The key cannot contains dot character: `'.$key.'`.');
            }
            static::$key_storage[] = $key;
            if (is_array($value)) {
                if (static::isIndexedArray($value) && !$options['skip_indexed_array']) {
                    static::serializeIndexedArray($value, $options);
                }
                else {
                    static::serialize($value, $options);
                }
            }
            else {
                static::$lines[] = array(
                    implode('.', static::$key_storage),
                    $value,
                );
            }
            array_pop(static::$key_storage);
        }
        return static::$lines;
    }

    protected static function serializeIndexedArray($array, $options)
    {
        $last = array_pop(static::$key_storage);
        $new_indexed_array_sorted = array();
        $new_indexed_array_non_sorted = array();
        foreach ($array as $k => $v) {
            if (static::$is_indexed_array_sorted) {
                $new_indexed_array_sorted[] = $v;
            }
            else {
                $new_indexed_array_non_sorted["$last"] = array('['."$k".']' => $v);
            }
        }
        if ($new_indexed_array_non_sorted) {
            static::serialize($new_indexed_array_non_sorted, $options);
        }
        if ($new_indexed_array_sorted) {
            // Backup $last ke property.
            static::$last = $last;
            static::serializeIndexedArraySorted($new_indexed_array_sorted, $options);
        }

    }

    protected static function serializeIndexedArraySorted($array, $options)
    {
        // Restore $last dari property.
        $last = static::$last;
        // Periksa seluruh value, apakah ada array atau tidak.
        $allowed = true;
        if ($options['indexed_array_append_format']) {
            foreach ($array as $k => $v) {
                if (is_array($v)) {
                    $allowed = false;
                    break;
                }
            }
        }
        foreach ($array as $k => $v) {
            $new_array = array();
            if (is_array($v)) {
                // Berarti ini masih belum selesai, maka tidak bisa
                // menggunakan append.
                $new_array["$last"] = array('['."$k".']' => $v);
            }
            else {
                if ($options['indexed_array_append_format'] && $allowed) {
                    $new_array["$last".'[]'] = $v;
                }
                else {
                    $new_array["$last"] = array('['."$k".']' => $v);
                }
            }
            static::serialize($new_array, $options);
        }
    }

    protected static function isIndexedArray($array)
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
                    static::$is_indexed_array_sorted = true;
                    continue;
                }
                else{
                    static::$is_indexed_array_sorted = false;
                    break;
                }
            }
            while (true);
            static::$is_indexed_array = true;
        }
        else {
            static::$is_indexed_array = false;
        }
        return static::$is_indexed_array;
    }
}
