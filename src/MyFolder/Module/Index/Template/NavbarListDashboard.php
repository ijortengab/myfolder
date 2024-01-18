<?php

namespace IjorTengab\MyFolder\Module\Index\Template;

use IjorTengab\MyFolder\Core\Application;

class NavbarListDashboard
{
    public function __toString()
    {
        return file_get_contents(Application::$cwd.'/templates/index/navbar-list-dashboard.html.twig');
    }
}
