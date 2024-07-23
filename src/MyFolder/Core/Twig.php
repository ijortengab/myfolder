<?php

namespace IjorTengab\MyFolder\Core;

class Twig
{
    protected $contents;
    protected $html= '';
    protected $matches_references = array();
    protected $cache_find = array();
    protected $cache_token = array();
    protected $placeholders;

    public function __construct($contents)
    {
        $this->contents = $contents;
    }

    public function __toString()
    {
        return $this->html;
    }

    public function render($placeholders = null)
    {
        // Kalo cuma angin, gak perlu structure.
        if (ctype_space($this->contents)) {
            return $this->contents;
        }
        $this->placeholders = (array) $placeholders;

        $structure = $this->parseContents();

        $html = '';
        $start = 0;
        $finish = strlen($this->contents);
        while ($info = array_shift($structure)){
            $tag_open_position = $info['position']['open'];
            $length = $tag_open_position - $start;
            $sub_contents_before = substr($this->contents, $start, $length);
            if (ctype_space($sub_contents_before)) {
                $html .= $sub_contents_before;
            }
            else {
                $html .= $this->subRender($sub_contents_before);
            }
            $html .= $this->resolveTag($info);
            $tag_close_position = $info['position']['close'];
            // Recalculate start variable.
            $start = $tag_close_position + strlen($this->matches_references[$tag_close_position][0]);
        }
        // Sisa contents kita render lagi.
        if ($start < $finish) {
            $length = $finish - $start;
            $sub_contents_before = substr($this->contents, $start, $length);
            if (ctype_space($sub_contents_before)) {
                $html .= $sub_contents_before;
            }
            else {
                $html .= $this->subRender($sub_contents_before);
            }
        }
        $this->html = $html;
        // Kembalikan object, agar bisa langsung di cetak dengan __toString.
        // Contoh: echo $twig->render(array());
        return $this;
    }

    /**
     * Khusus merender yang didalamnya tidak ada block tag seperti if, for.
     */
    protected function subRender($contents)
    {
        preg_match_all('/\{\{\s+([\w \"\'\?\|\.]+)\s+\}\}/', $contents, $matches, PREG_SET_ORDER);
        if (empty($matches)) {
            return $contents;
        }
        $jq = new JsonQuery($this->placeholders);
        while ($each = array_shift($matches)) {
            $replace = null;
            list($find, $something) = $each;
            if (array_key_exists($find, $this->cache_find)) {
                $contents = str_replace($find, $this->cache_find[$find], $contents);
                continue;
            }
            // Contoh: value.top ? 'checked'
            if (\str_contains($something, '?')) {
                preg_match('/(\S+)\s*\?\s*(\S+)/', $something, $matches3);
                list(,$parameter, $something) = $matches3;
                // Jika parameter terdapat karakter titik, maka parent array yang
                // menjadi placeholdernys.
                // Contoh:
                // $parameter = container.panel.body
                // Maka ubah $parameter menjadi body, lalu
                // Array container.panel menjadi $placeholders.
                $placeholders = $this->placeholders;
                if (($pos = strrpos($parameter, '.')) !== false) {
                    $path = substr($parameter, 0, $pos);
                    $parameter = substr($parameter, $pos+1);
                    $placeholders = (array) $jq->path('.'.$path)->get();
                }
                if (array_key_exists($parameter, $placeholders)) {
                    // https://twig.symfony.com/doc/3.x/tags/if.html.
                    $judge = (bool) $placeholders[$parameter];
                }
                else {
                    $judge = false;
                }
                if ($judge) {
                    if ($this->isQuoted($something)) {
                        $replace = substr($something, 1, -1);
                        $filter = 'raw';
                    }
                }
                else {
                    $replace = '';
                    $filter = 'raw';
                }
                // $replace = $judge ? $something: null;
            }
            if (!isset($replace)) {
                if (\str_contains($something, '|')) {
                    preg_match('/(\S+)\s*\|\s*(\S+)/', $something, $matches2);
                    list(,$token, $filter) = $matches2;
                }
                else {
                    $token = $something;
                    $filter = 'htmlentities';
                }
                if (!array_key_exists($token, $this->cache_token)) {
                    $this->cache_token[$token] = $jq->path('.'.$token)->get();
                }
                $replace = $this->cache_token[$token];
            }

            // Ubah boolean menjadi string.
            $replace = (string) $replace;
            switch ($filter) {
                case 'raw':
                    break;
                case 'htmlentities':
                    $replace = htmlentities($replace);
                    break;
                default:
                    // throw error @todo
                    break;
            }
            // Save translate-an ke cache.
            $this->cache_find[$find] = $replace;
            $contents = str_replace($find, $replace, $contents);
        }
        return $contents;
    }

    protected function resolveTag($info)
    {
        $html = '';
        switch ($info['block']) {
            case 'if':
                $html .= $this->resolveIfTag($info);
                break;

            case 'for':
                $html .= $this->resolveForTag($info);
                break;
        }
        return $html;
    }

    protected function resolveIfTag($info)
    {
        $html = '';
        $placeholders = $this->placeholders;
        $tag_open_position = $info['position']['open'];
        $references = $this->matches_references[$tag_open_position];

        list($start_tag, $parameter) = $references;
        // Jika parameter terdapat karakter titik, maka parent array yang
        // menjadi placeholdernys.
        // Contoh:
        // $parameter = container.panel.body
        // Maka ubah $parameter menjadi body, lalu
        // Array container.panel menjadi $placeholders.
        if (($pos = strrpos($parameter, '.')) !== false) {
            $path = substr($parameter, 0, $pos);
            $parameter = substr($parameter, $pos+1);
            $jq = new JsonQuery($this->placeholders);
            $placeholders = (array) $jq->path('.'.$path)->get();
        }
        if (array_key_exists($parameter, $placeholders)) {
            // https://twig.symfony.com/doc/3.x/tags/if.html.
            $judge = (bool) $placeholders[$parameter];
        }
        else {
            $judge = false;
        }
        if ($judge) {
            $start = $info['position']['open'] + strlen($start_tag);
            if ($info['position']['else'] === null) {
                $end = $info['position']['close'];
            }
            else {
                $end = $info['position']['else'];
            }
            $length = $end - $start;
            $sub_contents = substr($this->contents, $start, $length);
            $twig = new self($sub_contents);
            $html .= $twig->render($placeholders);
        }
        else {
            if ($info['position']['else'] === null) {
                $html .= '';
            }
            else {
                $tag_else_position = $info['position']['else'];
                $start = $tag_else_position + strlen($this->matches_references[$tag_else_position][0]);
                $end = $info['position']['close'];
                $length = $end - $start;
                $sub_contents = substr($this->contents, $start, $length);
                $twig = new self($sub_contents);
                $html .= $twig->render($placeholders);
            }
        }
        return $html;
    }

    protected function resolveForTag($info)
    {
        $html = '';
        $placeholders = $this->placeholders;
        $tag_open_position = $info['position']['open'];
        $references = $this->matches_references[$tag_open_position];
        list($start_tag, $token, $parameter) = $references;

        $jq = new JsonQuery($placeholders);
        $result = $jq->path('.'.$parameter)->get();
        if (is_array($result)) {
            if (empty($result)) {
                if ($info['position']['else'] === null) {
                    $html .= '';
                }
                else {
                    $tag_else_position = $info['position']['else'];
                    $start = $tag_else_position + strlen($this->matches_references[$tag_else_position][0]);
                    $end = $info['position']['close'];
                    $length = $end - $start;
                    $sub_contents = substr($this->contents, $start, $length);
                    $twig = new self($sub_contents);
                    $html .= $twig->render($placeholders);
                }
            }
            else {
                $start = $info['position']['open'] + strlen($start_tag);
                if ($info['position']['else'] === null) {
                    $end = $info['position']['close'];
                }
                else {
                    $end = $info['position']['else'];
                }
                $length = $end - $start;
                $sub_contents = substr($this->contents, $start, $length);
                if (substr_count($token, ',') === 0) {
                    $token_value = trim($token);
                    foreach ($result as $value) {
                        $_placeholders = array_merge($placeholders, array(
                            $token_value => $value,
                        ));
                        $twig = new self($sub_contents);
                        $html .= $twig->render($_placeholders);
                    }
                }
                else {
                    preg_match('/(\w+)\s*,\s*(\w+)/', $token, $matches);
                    list(,$token_key,$token_value) = $matches;
                    foreach ($result as $key => $value) {
                        $_placeholders = array_merge($placeholders, array(
                            $token_key => $key,
                            $token_value => $value,
                        ));
                        $twig = new self($sub_contents);
                        $html .= $twig->render($_placeholders);
                    }
                }
            }
        }
        return $html;
    }

    /**
     * Jika tag dikelilingi oleh line feed, maka koreksi value dari $position,
     * dan $info[0], ini untuk mencegah terjadinya blank line.
     */
    protected function addToReferences($position, $info)
    {
        // Resolve blank line.
        if (isset($this->contents[$position - 1])) {
            $p = $this->contents[$position - 1];
            $length = strlen($info[0]);
            if (isset($this->contents[$position + $length])) {
                $n = $this->contents[$position + $length];
                if ($p == "\n" && $n == "\n") {
                    $position -= 1;
                    $info[0] = "\n".$info[0];
                }
            }
        }
        $this->matches_references[$position] = $info;
        return array($position, $info[0]);
    }

    protected function parseContents()
    {
        // Contoh:
        // {{ apple }}
        // {% if orange %}
        //     Lorem Ipsum
        //     {{ grape }}
        //     Dolor sit amet
        //     {% for  b,c  in  address  %}
        //     {% endfor %}
        //     {% for a in schools %}
        //         {% if elementary %}
        //             go {{ a }}
        //             {% for a in fruits %}
        //                 Lorem Ipsum
        //             {% else %}
        //                 gak ada
        //                 {% if apple %}
        //                     {% if banana %}
        //                     {% endif %}
        //                 {% endif %}
        //             {% endfor %}
        //         {% else %}
        //         {% endif %}
        //     {% endfor %}
        // {% else %}
        //     oke donk
        // {% endif %}
        // {{ name }}
        // {{ name }}
        // {% if office %}
        // {% endif %}
        $storage = array();
        $contents = $this->contents;
        // Tag for.
        preg_match_all('/{%\s+for ([a-z, ]+) in\s+([\w \|\.]+)\s+%}/', $contents, $matches, PREG_SET_ORDER);
        // Find position and validate.
        $scope = $contents;
        $offset = 0;
        while($each = array_shift($matches)) {
            list($find,$placeholders,) = $each;
            if (substr_count($placeholders, ',') > 1) {
                // not valid.
                continue;
            };
            $distance = stripos($scope, $find);
            // Masukkan ke storage.
            $position = $distance + $offset;
            list($position, $find) = $this->addToReferences($position, $each);
            $storage[$position] = 'for';
            // Ubah scope pencarian.
            $offset += $distance + strlen($find);
            $scope = substr($contents, $offset);
        }
        // Tag endfor.
        preg_match_all('/{%\s+endfor\s+%}/', $contents, $matches, PREG_SET_ORDER);
        // Find position.
        $scope = $contents;
        $offset = 0;
        while($each = array_shift($matches)) {
            $find = current($each);
            $distance = stripos($scope, $find);
            // Masukkan ke storage.
            $position = $distance + $offset;
            list($position, $find) = $this->addToReferences($position, $each);
            $storage[$position] = 'endfor';
            // Ubah scope pencarian.
            $offset += $distance + strlen($find);
            $scope = substr($contents, $offset);
        }
        // Tag if.
        preg_match_all('/{%\s+if\s+([\w \|\.]+)\s+%}/', $contents, $matches, PREG_SET_ORDER);
        // Find position.
        $scope = $contents;
        $offset = 0;
        while($each = array_shift($matches)) {
            list($find,) = $each;
            $distance = stripos($scope, $find);
            // Masukkan ke storage.
            $position = $distance + $offset;
            list($position, $find) = $this->addToReferences($position, $each);
            $storage[$position] = 'if';
            // Ubah scope pencarian.
            $offset += $distance + strlen($find);
            $scope = substr($contents, $offset);
        }
        // Tag endif.
        preg_match_all('/{%\s+endif\s+%}/', $contents, $matches, PREG_SET_ORDER);
        // Find position.
        $scope = $contents;
        $offset = 0;
        while($each = array_shift($matches)) {
            $find = current($each);
            $distance = stripos($scope, $find);
            // Masukkan ke storage.
            $position = $distance + $offset;
            list($position, $find) = $this->addToReferences($position, $each);
            $storage[$position] = 'endif';
            // Ubah scope pencarian.
            $offset += $distance + strlen($find);
            $scope = substr($contents, $offset);
        }
        // Tag else.
        preg_match_all('/{%\s+else\s+%}/', $contents, $matches, PREG_SET_ORDER);
        // Find position.
        $scope = $contents;
        $offset = 0;
        while($each = array_shift($matches)) {
            $find = current($each);
            $distance = stripos($scope, $find);
            // Masukkan ke storage.
            $position = $distance + $offset;
            list($position, $find) = $this->addToReferences($position, $each);
            $storage[$position] = 'else';
            // Ubah scope pencarian.
            $offset += $distance + strlen($find);
            $scope = substr($contents, $offset);
        }
        if (empty($storage)) {
            return $storage;
        }
        ksort($storage);
        // Hasil dari var_export($storage) adalah:
        // var_export($storage);
        // $storage = array (
        //   12 => 'if',
        //   83 => 'for',
        //   116 => 'endfor',
        //   133 => 'for',
        //   164 => 'if',
        //   219 => 'for',
        //   281 => 'else',
        //   332 => 'if',
        //   367 => 'if',
        //   403 => 'endif',
        //   431 => 'endif',
        //   455 => 'endfor',
        //   476 => 'else',
        //   495 => 'endif',
        //   511 => 'endfor',
        //   524 => 'else',
        //   548 => 'endif',
        //   582 => 'if',
        //   598 => 'endif',
        // );
        // Go structure.
        $structure = $this->createStructure($storage);
        // Hasil dari var_dump($structure) adalah:
        // var_dump($structure);
        // array(2) {
        //   [12]=> object(TwigStructureHelper)#3 (12) {
        //     "path" => "/if"
        //     "key" => 12
        //     "value" => "if"
        //     "parent" => NULL
        //     "tag_open_position" => 12
        //     "tag_else_position" => 524
        //     "tag_close_position" => 548
        //     "block" => "if"
        //     "childrens" => array(2) {
        //       [83]=> object(TwigStructureHelper)#4 (12) {
        //         "path" => "/if/for"
        //         "key" => 83
        //         "value" => "for"
        //         "parent" => *RECURSION*
        //         "tag_open_position" => 83
        //         "tag_else_position" => NULL
        //         "tag_close_position" => 116
        //         "block" => "for"
        //         "childrens" => array(0) {
        //         }
        //       }
        //       [133]=> object(TwigStructureHelper)#6 (12) {
        //         "path" => "/if/for"
        //         "key" => 133
        //         "value" => "for"
        //         "parent" => *RECURSION*
        //         "tag_open_position" => 133
        //         "tag_else_position" => NULL
        //         "tag_close_position" => 511
        //         "block" => "for"
        //         "childrens" => array(1) {
        //           [164]=> object(TwigStructureHelper)#5 (12) {
        //             "path" => "/if/for/if"
        //             "key" => 164
        //             "value" => "if"
        //             "parent" => *RECURSION*
        //             "tag_open_position" => 164
        //             "tag_else_position" => 476
        //             "tag_close_position" => 495
        //             "block" => "if"
        //             "childrens" => array(1) {
        //               [219]=> object(TwigStructureHelper)#7 (12) {
        //                 "path" => "/if/for/if/for"
        //                 "key" => 219
        //                 "value" => "for"
        //                 "parent" => *RECURSION*
        //                 "tag_open_position" => 219
        //                 "tag_else_position" => 281
        //                 "tag_close_position" => 455
        //                 "block" => "for"
        //                 "childrens" => array(1) {
        //                   [332]=> object(TwigStructureHelper)#9 (12) {
        //                     "path" => "/if/for/if/for/if"
        //                     "key" => 332
        //                     "value" => "if"
        //                     "parent" => *RECURSION*
        //                     "tag_open_position" => 332
        //                     "tag_else_position" => NULL
        //                     "tag_close_position" => 431
        //                     "block" => "if"
        //                     "childrens" => array(1) {
        //                       [367]=> object(TwigStructureHelper)#8 (12) {
        //                         "path" => "/if/for/if/for/if/if"
        //                         "key" => 367
        //                         "value" => "if"
        //                         "parent" => *RECURSION*
        //                         "tag_open_position" => 367
        //                         "tag_else_position" => NULL
        //                         "tag_close_position" => 403
        //                         "block" => "if"
        //                         "childrens" => array(0) {
        //                         }
        //                       }
        //                     }
        //                   }
        //                 }
        //               }
        //             }
        //           }
        //         }
        //       }
        //     }
        //   }
        //   [582]=> object(TwigStructureHelper)#10 (12) {
        //     "path" => "/if"
        //     "key" => 582
        //     "value" => "if"
        //     "parent" => NULL
        //     "tag_open_position" => 582
        //     "tag_else_position" => NULL
        //     "tag_close_position" => 598
        //     "block" => "if"
        //     "childrens" => array(0) {
        //     }
        //   }
        // }
        // Simplifikasi.
        $structure_to_array = array();
        foreach ($structure as $tag_open_position => $object) {
            $structure_to_array[$tag_open_position] = $this->structureToArray($object);
        }
        // Hasil dari var_dump($structure_to_array) adalah:
        // var_dump($structure_to_array);
        // array(2) {
        //   [12]=> array(4) {
        //     "block" => "if"
        //     "path" => "/if"
        //     "position" => array(3) {
        //       "open" => 12
        //       "else" => 524
        //       "close" => 548
        //     }
        //     "children" => array(4) {
        //       [83]=> array(5) {
        //         "block" => "for"
        //         "path" => "/if/for"
        //         "position" => array(3) {
        //           "open" => 83
        //           "else" => NULL
        //           "close" => 116
        //         }
        //         "children" => array(0) {
        //         }
        //       }
        //       [133]=> array(4) {
        //         "block" => "for"
        //         "path" => "/if/for"
        //         "position" => array(3) {
        //           "open" => 133
        //           "else" => NULL
        //           "close" => 511
        //         }
        //         "children" => array(1) {
        //           [164]=> array(4) {
        //             "block" => "if"
        //             "path" => "/if/for/if"
        //             "position" => array(3) {
        //               "open" => 164
        //               "else" => 476
        //               "close" => 495
        //             }
        //             "children" => array(1) {
        //               [219]=> array(4) {
        //                 "block" => "for"
        //                 "path" => "/if/for/if/for"
        //                 "position" => array(3) {
        //                   "open" => 219
        //                   "else" => 281
        //                   "close" => 455
        //                 }
        //                 "children" => array(1) {
        //                   [332]=> array(4) {
        //                     "block" => "if"
        //                     "path" => "/if/for/if/for/if"
        //                     "position" => array(3) {
        //                       "open" => 332
        //                       "else" => NULL
        //                       "close" => 431
        //                     }
        //                     "children" => array(1) {
        //                       [367]=> array(4) {
        //                         "block" => "if"
        //                         "path" => "/if/for/if/for/if/if"
        //                         "position" => array(3) {
        //                           "open" => 367
        //                           "else" => NULL
        //                           "close" => 403
        //                         }
        //                         "children" => array(0) {
        //                         }
        //                       }
        //                     }
        //                   }
        //                 }
        //               }
        //             }
        //           }
        //         }
        //       }
        //     }
        //   }
        //   [582]=> array(4) {
        //     "block" => "if"
        //     "path" => "/if"
        //     "position" => array(3) {
        //       "open" => 582
        //       "else" => NULL
        //       "close" => 598
        //     }
        //     "children" => array(0) {
        //     }
        //   }
        // }
        //
        return $structure_to_array;
    }

    private function createStructure($storage)
    {
        // Validate.
        // Jumlah element harus genap.
        // if (count($storage) % 2 === 1) {
            // throw new RuntimeException('Template invalid, missing tag open or tag close.');
        // }

        $key = key($storage);
        $value = current($storage);
        $current = new TwigStructureHelper($key, $value);
        $storage_new[$key] = $current;
        $x = 0;
        while (next($storage)) {
            $key = key($storage);
            $value = current($storage);
            $prev = $current;
            $current = new TwigStructureHelper($key, $value);
            $p = $prev;
            $c = $current;
            if ($prev->isTagOpen() && $current->isTagOpen()) {
                // Contoh:
                //   12 => 'if',
                //   83 => 'for',
                $current->setParent($prev);
                $prev->addChild($current);
            }
            elseif ($prev->isTagOpen() && $current->isTagClose()) {
                // Contoh:
                //   83 => 'for',
                //   116 => 'endfor',
                // atau
                //   12 => 'if',
                //   548 => 'endif',
                $prev->setAsCloseTag($current);
                $current = $prev->getParent();
                // $current bernilai NULL, jika $prev tidak
                // punya parent atau deep struktur level 0.
            }
            elseif ($prev->isTagOpen() && $current->isTagElse()) {
                // Contoh:
                //   219 => 'for',
                //   281 => 'else',
                $prev->setAsElseTag($current);
                $current = $prev;
            }
            else {
                var_dump($p);
                var_dump($c);
                die('Belum ketemu kasus.');
            }
            if ($current === null) {
                if (next($storage)) {
                    $key = key($storage);
                    $value = current($storage);
                    $current = new TwigStructureHelper($key, $value);
                    $storage_new[$key] = $current;
                }
            }
            $x++;
            // Untuk kepentingan debug.
            // if ($x == 1) {
                // var_dump($p);
                // var_dump($c);
                // die('Debug.');
            // }
        }
        return $storage_new;
    }

    private function structureToArray($object)
    {
        $array = array(
            'block' => (string) $object,
            'path' => $object->getPath(),
            'position' => array(),
            'children' => array(),
        );
        list($open, $else, $close) = $object->getPosition();
        $array['position']['open'] = $open;
        $array['position']['else'] = $else;
        $array['position']['close'] = $close;
        foreach ($object->getChildrens() as $_open => $_object) {
            $array['children'][$_open] = $this->structureToArray($_object);
        }
        return $array;
    }

    private function isQuoted($string) {
        if (($left = substr($string, 0, 1)) && ($right = substr($string, -1)) && $left === $right && in_array($left, array('"', "'"))) {
            return true;
        }
        return false;
    }
}
