<?php

namespace IjorTengab\MyFolder\Module\Index\Template;

class NavbarListDashboard
{
    public function __toString()
    {
        ob_start(); include('templates/index/navbar-list-dashboard.html.twig'); return ob_get_clean();
    }
}
