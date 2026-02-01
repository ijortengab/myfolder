<?php

namespace IjorTengab\MyFolder\Module\User\Template;

class NavbarListLogin 
{
    public function __toString()
    {
        ob_start(); include('templates/user/navbar-list-login.html.twig'); return ob_get_clean();
    }
}
