<?php

namespace IjorTengab\MyFolder\Module\Index\Asset;

class IndexClass
{
    public function __toString()
    {
        return file_get_contents(getcwd().'/assets/index/index-class.js');
    }
}