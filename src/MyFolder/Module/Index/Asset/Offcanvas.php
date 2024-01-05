<?php

namespace IjorTengab\MyFolder\Module\Index\Asset;

class Offcanvas
{
    public function __toString()
    {
        return file_get_contents(getcwd().'/assets/index/offcanvas.js');
    }
}
