<?php

namespace IjorTengab\MyFolder\Module\User\Template;

class NavbarListLogin 
{
    public function __toString()
    {
        return file_get_contents(getcwd().'/templates/user/navbar-list-login.html.twig');
    }
}
