<?php

namespace IjorTengab\MyFolder\Module\Index\Asset;

class Modal
{
    public function __toString()
    {
        return file_get_contents(getcwd().'/assets/index/modal.js');
    }
}
