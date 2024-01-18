<?php

namespace IjorTengab\MyFolder\Module\Index\Template;

use IjorTengab\MyFolder\Core\Application;

class Index
{
    public function __toString()
    {
        return file_get_contents(Application::$cwd.'/templates/index/index.html.twig');
    }
}
