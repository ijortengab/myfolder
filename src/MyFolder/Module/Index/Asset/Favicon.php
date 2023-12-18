<?php

namespace IjorTengab\MyFolder\Module\Index\Asset;

class Favicon
{
    public function __toString()
    {
        return file_get_contents(getcwd().'/assets/index/favicon.svg');
    }
}
