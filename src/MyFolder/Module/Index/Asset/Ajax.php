<?php

namespace IjorTengab\MyFolder\Module\Index\Asset;

class Ajax {
    public function __toString()
    {
        return file_get_contents(getcwd().'/assets/index/ajax.js');
    }
}
