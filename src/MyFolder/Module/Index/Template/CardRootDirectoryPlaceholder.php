<?php

namespace IjorTengab\MyFolder\Module\Index\Template;

use IjorTengab\MyFolder\Core\Application;

class CardRootDirectoryPlaceholder
{
    public function __toString()
    {
        return file_get_contents(Application::$cwd.'/templates/index/card-root-directory-placeholder.html.twig');
    }
}
