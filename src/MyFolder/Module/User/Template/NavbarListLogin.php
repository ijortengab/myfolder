<?php

namespace IjorTengab\MyFolder\Module\User\Template;

use IjorTengab\MyFolder\Core\Application;

class NavbarListLogin 
{
    public function __toString()
    {
        return file_get_contents(Application::$cwd.'/templates/user/navbar-list-login.html.twig');
    }
}
