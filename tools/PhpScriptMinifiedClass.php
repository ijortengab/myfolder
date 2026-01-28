<?php

namespace IjorTengab\MyFolder\Tools;

class PhpScriptMinifiedClass
{
    protected $namespace;
    protected $filename;
    protected $class_name;
    protected $class_start_line;
    protected $class_end_line;
    protected $add_doc_comment = false;
    protected $add_doc_comment_contents;
    protected $use_opening_tag = true;
    protected $use_namespace = true;
    protected $use_alias = true;

    // Output of function file(). Array.
    protected $lines_raw;

    // String.
    protected $head_string;
    protected $aliases = array();
    protected $body_string = '';

    /**
     *
     */
    public function __construct($namespace, $filename)
    {
        $this->namespace = $namespace;
        $this->filename = $filename;
        $this->minified();
        return $this;
    }

    /**
     *
     */
    public function __toString()
    {
        $namespace = $this->namespace;
        $contents = array();
        if ($this->use_opening_tag) {
            $contents[] = '<?php';
            $contents[] = '';
        }
        if ($this->use_namespace) {
            $contents[] = 'namespace '."$namespace".';';
            $contents[] = '';
        }
        if ($this->use_alias && count($this->aliases)) {
            $aliases = array_map('rtrim', $this->aliases);
            $contents[] = implode("\n",$aliases);
            $contents[] = '';
        }

        if (strlen($this->body_string)) {
            $this->head_string .= ' {';
            $this->body_string .= '}';
        }

        if ($this->add_doc_comment) {
            $contents[] = '/**';
            if (empty($this->add_doc_comment_contents)) {
                $contents[] = ' *';
            }
            else {
                foreach ($this->add_doc_comment_contents as $each) {
                    $contents[] = ' * '.$each;
                }
            }
            $contents[] = ' */';
        }
        $contents[] = $this->head_string;
        if (strlen($this->body_string)) {
            $contents[] = $this->body_string;
        }
        return implode("\n", $contents);
    }

    /**
     *
     */
    public function addDocComment($array = null)
    {
        $this->add_doc_comment = true;
        $this->add_doc_comment_contents = $array;
        return $this;
    }

    /**
     *
     */
    public function stripOpeningTag()
    {
        $this->use_opening_tag = false;
        return $this;
    }

    /**
     *
     */
    public function stripNameSpace()
    {
        $this->use_namespace = false;
        return $this;
    }
    /**
     *
     */
    public function stripAlias()
    {
        $this->use_alias = false;
        return $this;
    }

    public function getAlias()
    {
        $aliases = array_map('rtrim', $this->aliases);
        return array_values($aliases);
    }

    protected function minified()
    {
        $this->class_name = pathinfo($this->filename, PATHINFO_FILENAME);
        $reflection_class = new \ReflectionClass($this->namespace."\\".$this->class_name);
        $this->class_start_line = $reflection_class->getStartLine();
        $this->class_end_line = $reflection_class->getEndLine();
        $this->populateAliases();
        $this->populateHead();
        $this->populateBody($reflection_class);
    }

    /**
     *
     */
    protected function populateAliases()
    {
        $start_line = $this->class_start_line;

        // use_alias
        $index = $start_line - 1;
        if (null === $this->lines_raw) {
            $this->lines_raw = file($this->filename);
        }
        $lines = $this->lines_raw;
        array_splice($lines, $index);

        $tokens = token_get_all(implode("", $lines));
        while($token = array_shift($tokens)) {
            if (!is_array($token)) {
                continue;
            }
            list($code,,$line) = $token;
            if ($code === T_USE) {
                $this->aliases[$line] = $this->lines_raw[--$line];
            }
        }
    }

    /**
     *
     */
    protected function populateHead()
    {
        $start_line = $this->class_start_line;
        $index = $start_line - 1;
        if (null === $this->lines_raw) {
            $this->lines_raw = file($this->filename);
        }
        $this->head_string = rtrim($this->lines_raw[$index]);
    }

    protected function populateBody($reflection_class)
    {
        $start_line = $this->class_start_line;
        $end_line = $this->class_end_line;
        $methods =  $reflection_class->getMethods();

        $methods_array_one_line = array();
        $collect_lines = array();
        while ($reflection_method = array_shift($methods)) {
            $method_start_line = $reflection_method->getStartLine();
            if ($method_start_line === false) {
                continue;
            }
            $class_current = $this->namespace.'\\'.$this->class_name;
            $class_owner = $reflection_method->class;
            if ($class_current != $class_owner) {
                continue;
            }
            $method_name = $reflection_method->getName();
            $method_end_line = $reflection_method->getEndLine();
            $doc_comment = $reflection_method->getDocComment();
            $additional_lines = 0;
            if ($doc_comment) {
                $count_chars = count_chars($doc_comment, 1);
                if (array_key_exists(10, $count_chars)) {
                    $additional_lines = $count_chars[10] + 1;
                }
            }
            $collect_lines[$method_start_line] = array($method_start_line, $method_end_line, $additional_lines);
            $this->processMethodBody($method_name, $method_start_line, $method_end_line);
        }
        $this->processPropertyBody($collect_lines);
    }

    /**
     *
     */
    protected function processMethodBody($method_name, $start_line, $end_line)
    {
        if (null === $this->lines_raw) {
            $this->lines_raw = file($this->filename);
        }
        $class_name = $this->class_name;
        $class_start_line = $this->class_start_line;
        $class_end_line = $this->class_end_line;
        $lines = $this->lines_raw;
        array_splice($lines, 0, $start_line - 1);
        $lines_sliced = array_slice($lines, 0, ($end_line - $start_line) + 1);

        $function_name = "\\IjorTengab\\MyFolder\\Tools\\${class_name}__${method_name}__stringify";
        if (function_exists($function_name)) {
            $string = (string) $function_name($lines_sliced);
        }
        else {
            $string = (string) static::stringifyMethod($lines_sliced);
            $string = '  '.$string;
        }
        $this->body_string .= $string."\n";
    }

    protected function processPropertyBody($collect_lines)
    {
        if (null === $this->lines_raw) {
            $this->lines_raw = file($this->filename);
        }
        $class_name = $this->class_name;
        $class_start_line = $this->class_start_line;
        $class_end_line = $this->class_end_line;
        $lines = $this->lines_raw;
        array_splice($lines, $class_end_line);

        while ($last = array_pop($collect_lines)) {
            list($start, $end, $add) = $last;
            if ($add > 0) {
                $start -= $add;
            }
            $length = $end - $start;
            array_splice($lines, $start - 1, $length + 1);
        }
        $lines_sliced = array_slice($lines, $class_start_line - 1);

        // Jika standardnya begini,
        // class AccessControl
        // {
        // }
        // maka: hapus 2 pertama, buang 1 terakhir.
        array_shift($lines_sliced);
        array_shift($lines_sliced);
        array_pop($lines_sliced);

        $function_name = "\\IjorTengab\\MyFolder\\Tools\\${class_name}__stringify";

        if (function_exists($function_name)) {
            $string = (string) $function_name($lines_sliced);
        }
        else {
            $string = (string) static::stringifyProperties($lines_sliced);
        }
        if (strlen($string)) {
            $string .= "\n";
        }
        $this->body_string = $string.$this->body_string;
    }

    /**
     * Biasanya untuk method tanpa heredoc dengan baris terlalu banyak.
     */
    public static function stringifyMethod(array $array)
    {
        $array = array_map('trim', $array);
        $array = array_filter($array, function ($value) {
            if (strpos($value, '//') === 0) {
                return false;
            }
            elseif($value === '') {
                return false;
            }
            return true;
        });
        // Jika standardnya begini,
        // public static function route()
        // {
        // }
        // maka: lepas sementara 2 pertama, dan 1 terakhir.
        // $array = array_map('ltrim', $array);
        $a = array_shift($array);
        $b = array_shift($array);
        $z = array_pop($array);

        // Mulai kita jadikan sebagai PHP Script.
        array_walk($array, function (&$value) {
            $value .= "\n";
        });
        $contents = implode("", $array);
        $contents = "<?php\n".$contents;

        // Parsing.
        $lines = PhpScriptMinified::minified($contents);
        array_walk($lines, function (&$value) {
            $value = implode('', $value);
        });

        $lines = array_map('trim', $lines);
        array_walk($lines, function (&$value) {
            $value .= "\n";
        });
        $lines = array_filter($lines, function ($value) {
            return !($value === "\n");
        });

        // Hapus opening tag: "<?php".
        array_shift($lines);
        // Kasih indent.
        array_walk($lines, function (&$value) {
            $value = '    '.$value;
        });
        // Bangun ulang heading.
        $ab = array($a, $b);
        $ab = implode(' ', $ab);
        // ~Kasih indent heading~ Indent sudah dikasih oleh parent process.
        // $ab = '  '.$ab;
        $ab .= "\n";
        array_unshift($lines, $ab);
        // Kasih indent footing.
        $z = '  '.$z."\n";
        array_push($lines, $z);

        // Empty string must skip, caranya:
        // kasih LINE FEED terlebih dahulu di setiap baris.
        // array_walk($lines, function (&$sentences) {
            // $sentences .= "\n";
        // });
        $string = implode('',$lines);
        // Hapus \n terakhir.
        $string = substr($string, 0, -1);
        return $string;
    }

    /**
     * Biasanya untuk non method (property,constant) sederhana tanpa heredoc.
     */
    public static function stringifyProperties(array $array)
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
        array_walk($filtered, function (&$value) {
            $value = '  '.$value;
        });
        $string = implode("\n", $filtered);
        return $string;
    }

    /**
     * Untuk method yang terdapat heredoc.
     */
    public static function stringifyMethodContainsHeredoc(array $array)
    {
        $heredoc_mode = false;
        $heredoc_mark = null;
        $contents = array();
        $string = '  ';
        foreach ($array as $line) {
            $line = rtrim($line);
            $contents[] = $line;
            if (strpos($line, '<<<')) {
                $heredoc_mode = true;
                preg_match('/<<<(\'?[a-zA-Z]+\'?)$/', $line, $matches);
                list(,$heredoc_mark) = $matches;
                $heredoc_mark = trim($heredoc_mark,"'");
                $string .= self::stringifyMethod($contents);
                $string .= "\n";
                $contents = array();
            }
            if ($line == "${heredoc_mark};") {
                $heredoc_mode = false;
                $heredoc_mark = null;
                $string .= implode("\n", $contents). ' ';
                $contents = array();
            }
        }
        $string .= self::stringifyMethod($contents);
        return $string;
    }

    /**
     * Untuk method yang terdapat heredoc.
     */
    public static function stringifyPropertiesContainsHeredoc(array $array)
    {
        $storage = array();
        $context = 0;
        $heredoc_mode = false;
        $heredoc_mark = null;
        $contents = array();
        $string = '';
        foreach ($array as $line) {
            $line = rtrim($line);
            $contents[] = $line;
            if (strpos($line, '<<<')) {
                $heredoc_mode = true;
                preg_match('/<<<(\'?[a-zA-Z]+\'?)$/', $line, $matches);
                list(,$heredoc_mark) = $matches;
                $heredoc_mark = trim($heredoc_mark,"'");
                $string .= static::stringifyProperties($contents);
                $string .= "\n";
                $contents = array();
            }
            if ($line == "${heredoc_mark};") {
                $heredoc_mode = false;
                $heredoc_mark = null;
                $string .= implode("\n", $contents). "\n";
                $contents = array();
            }
        }
        $string .= static::stringifyProperties($contents);
        return $string;
    }
}
