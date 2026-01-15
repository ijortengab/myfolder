<?php

namespace IjorTengab\MyFolder\Core;

class MathematicalLogic
{
    protected $binary;

    protected $callback;

    protected $debug = array();

    // Level 0: starting
    // Contoh:
    // "([core:follow_parent_access]&[user:is_sysadmin])|[user:is_sysadmin]|([core:follow_parent_access]&[user:is_sysadmin])|(([core:follow_parent_access]&[user:is_sysadmin]|([core:follow_parent_access]&[user:is_sysadmin])))"
    protected $debug_1;

    // Level 1: inside_braces_found
    // Contoh:
    // "[core:follow_parent_access]&[user:is_sysadmin]"
    protected $debug_2;

    // Level 2: token_ready
    // Contoh:
    // "[core:follow_parent_access]&[user:is_sysadmin]"
    protected $debug_3;

    // Level 2: token_translated
    // Contoh:
    // "1&0"
    protected $debug_4;

    // Level 2: token_merge
    // Contoh:
    // "0"
    protected $debug_5;

    // Level 1: inside_braces_translated
    // Contoh:
    // "0|0|0|0"
    protected $debug_6;

    // Level 1: inside_braces_merge
    // Contoh:
    // "0"
    protected $debug_7;

    // Level 0: finishing
    // Contoh:
    // "0"
    protected $debug_8;

    public function __construct($binary, callable $callback = null)
    {
        $this->binary = $binary;
        $this->callback = $callback;
        return $this;
    }

    public function prove()
    {
        $x = 0;
        while (!in_array($this->binary, array('0', '1'))) {
            // unset($matches);
            $this->debug_1 = $this->binary;
            $this->debug[] = $this->debug_1;

            if (!preg_match('/\([^\(\)]+\)/', $this->binary, $matches)) {
                $this->binary = $this->conditionToBinary($this->binary);
            }
            else {
                // Terdapat karakter kurung buka dan kurung tutup.
                $find = array_shift($matches);
                $inside_braces = trim($find, '()');
                $replace = $this->conditionToBinary($inside_braces);
                $this->binary = preg_replace('/'.preg_quote($find).'/', $replace, $this->binary, 1);
            };
            $this->debug_8 = $this->binary;

            $x++;
            // Limit adalah 100.
            if ($x > 100) {
                // @todo throw error Logic.
                break;
            }
        }
        $this->debug[] = $this->debug_8;
        $debugname = 'debug'; $debugfile = 'debug.html'; $debugvariable = '|||wakwaw|||'; if (array_key_exists($debugname, get_defined_vars())) { $debugvariable = $$debugname; } elseif (isset($this) && property_exists($this, $debugname)) { $debugvariable = $this->{$debugname}; $debugname = '$this->' . $debugname; } if ($debugvariable !== '|||wakwaw|||') { ob_start(); echo "\r\n<pre>" . basename(__FILE__ ). ":" . __LINE__ . " (Time: " . date('c') . ", Direktori: " . dirname(__FILE__) . ")\r\n". 'var_dump(' . $debugname . '): '; var_dump($debugvariable); echo "</pre>\r\n"; $debugoutput = ob_get_contents();ob_end_clean(); file_put_contents($debugfile, $debugoutput, FILE_APPEND); }

        return $this->binary === '1' ? true : false;
    }

    protected function conditionToBinary($condition)
    {
        if (in_array($condition, array('0', '1'))) {
            return $condition;
        }
        $binary = $this->conditionToBinaryOr($condition);
        $this->debug_7 = $binary;
        return $binary;
    }

    protected function conditionToBinaryOr($condition)
    {
        if (in_array($condition, array('0', '1'))) {
            return $condition;
        }

        $this->debug_2 = $condition;

        $or = '';
        $array = explode('|', $condition);

        $this->debug_6 = array();
        foreach ($array as $each) {
            if (in_array($each, array('0', '1'))) {
                $binary = $each;
            }
            else {
                $binary = $this->conditionToBinaryAnd($each);
                $this->debug_5 = $binary;
            }
            $or .= $binary;
            $this->debug_6[] = $binary;
        }
        $this->debug_6 = implode('|', $this->debug_6);

        return str_contains($or, '1') ? '1' : '0';
    }

    protected function conditionToBinaryAnd($condition)
    {
        $this->debug_3 = $condition;

        $and = '';
        $array = explode('&', $condition);

        $this->debug_4 = array();
        foreach ($array as $each) {
            if (in_array($each, array('0', '1'))) {
                $binary = $each;
            }
            else {
                $binary = $this->tokenToBinary($each);
            }
            $and .= $binary;
            $this->debug_4[] = $binary;
        }
        $this->debug_4 = implode('&', $this->debug_4);

        if ($this->debug_1 == $this->debug_2) {
            // Contoh kasus: $this->debug_1 = "0|z|0|0"
            //               $this->debug_2 = "0|z|0|0"
            // Pada kondisi awal: $this->debug_1 = "(a&z|z)|z|(a&z)|((a&z|(a&z)))"
            $pos_1 = 0;
            $pos_2 = strpos($this->debug_2, $this->debug_3);
        }
        else {
            $pos_1 = strpos($this->debug_1, '('.$this->debug_2.')');
            $pos_2 = strpos('('.$this->debug_2.')', $this->debug_3);
        }

        if ($this->debug_1 == $this->debug_2) {
            $segmen_2 = substr($this->debug_2, 0, $pos_2) . $this->debug_4 . substr($this->debug_2, $pos_2 + strlen($this->debug_3));
            $segmen_1 = substr($this->debug_1, 0, $pos_1) . $segmen_2 . substr($this->debug_1, $pos_1 + strlen($this->debug_2));
            $this->debug_2 = $segmen_2;
        }
        else {
            $segmen_2 = substr('('.$this->debug_2.')', 0, $pos_2) . $this->debug_4 . substr('('.$this->debug_2.')', $pos_2 + strlen($this->debug_3));
            $segmen_1 = substr($this->debug_1, 0, $pos_1) . $segmen_2 . substr($this->debug_1, $pos_1 + strlen('('.$this->debug_2.')'));
            $this->debug_2 = trim($segmen_2,'()');
        }
        $this->debug_1 = $segmen_1;
        $this->debug[] = $this->debug_1;
        return str_contains($and, '0') ? '0' : '1';
    }

    protected function tokenToBinary($token)
    {
        return call_user_func_array($this->callback, array($token));
    }
}
