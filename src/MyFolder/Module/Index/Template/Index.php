<?php

namespace IjorTengab\MyFolder\Module\Index\Template;

class Index
{
    public function __toString()
    {
        return file_get_contents(getcwd().'/templates/index/index.html.twig');
    }
}
