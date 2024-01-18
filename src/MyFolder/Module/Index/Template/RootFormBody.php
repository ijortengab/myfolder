<?php

namespace IjorTengab\MyFolder\Module\Index\Template;

use IjorTengab\MyFolder\Core\Application;

class RootFormBody
{
    public function __toString()
    {
        return file_get_contents(Application::$cwd.'/templates/index/root-form-body.html.twig');
    }
}
