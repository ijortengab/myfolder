<?php

namespace IjorTengab\MyFolder\Module\Index\Template;

class NavbarListDashboard
{
    public function __toString()
    {
        return file_get_contents(getcwd().'/templates/index/navbar-list-dashboard.html.twig');
    }
}
