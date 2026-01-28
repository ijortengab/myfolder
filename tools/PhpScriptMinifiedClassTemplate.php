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
            if (strpos($line, 'file_get_contents')) {
                $last = array_pop($contents);

                $last = str_replace('Application::$cwd.\'', '\''.getcwd(), $last);
                preg_match('/^(.*)return\s+file_get_contents\(\'(.*)\'\);$/', $last, $matches);
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
