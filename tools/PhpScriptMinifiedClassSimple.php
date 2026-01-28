<?php

namespace IjorTengab\MyFolder\Tools;

class PhpScriptMinifiedClassSimple extends PhpScriptMinifiedClass
{
    /**
     * Biasanya untuk method sederhana tanpa heredoc.
     */
    public static function stringifyMethod(array $array)
    {
        $trimmed = array_map('trim', $array);
        $filtered = array_filter($trimmed, function ($value) {
            if (strpos($value, '//') === 0) {
                return false;
            }
            elseif($value === '') {
                return false;
            }
            return true;
        });
        $string = implode(' ', $filtered);
        return $string;
    }
}
