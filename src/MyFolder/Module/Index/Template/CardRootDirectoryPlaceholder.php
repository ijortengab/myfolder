<?php

namespace IjorTengab\MyFolder\Module\Index\Template;

class CardRootDirectoryPlaceholder
{
    public function __toString()
    {
        return file_get_contents(getcwd().'/templates/index/card-root-directory-placeholder.html.twig');
    }
}
