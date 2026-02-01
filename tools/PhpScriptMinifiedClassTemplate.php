<?php

namespace IjorTengab\MyFolder\Tools;

class PhpScriptMinifiedClassTemplate extends PhpScriptMinifiedClass
{
    /**
     * Biasanya untuk method sederhana tanpa heredoc.
     */
    public static function stringifyMethod(array $array)
    {
        $contents = array();
        foreach ($array as $line) {
            $contents[] = $line;
            if (strpos($line, 'include')) {
                $last = array_pop($contents);

                $last = str_replace('include(\'', 'include(\''.getcwd().'/', $last);
                preg_match('/^(.*)include\(\'(.*)\'\)/', $last, $matches);
                list(,,$asset_path) = $matches;
                $contents[] = 'return <<<'."'MYFOLDER'";
                $contents[] = file_get_contents($asset_path);
                $contents[] = '';
                $contents[] = 'MYFOLDER;';
                $contents[] = '';
            }
        }
        $array = $contents;
        return static::stringifyMethodContainsHeredoc($array);
    }
}
