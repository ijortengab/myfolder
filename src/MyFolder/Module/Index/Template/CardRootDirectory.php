<?php

namespace IjorTengab\MyFolder\Module\Index\Template;

class CardRootDirectory
{
    public function __toString()
    {
        return file_get_contents(getcwd().'/templates/index/card-root-directory.html.twig');
    }
}
