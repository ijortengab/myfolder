<?php

namespace IjorTengab\MyFolder\Module\User\Template;

class CardUserSysAdminCreate 
{
    public function __toString()
    {
        ob_start(); include('templates/user/card-user-sysadmin-create.html.twig'); return ob_get_clean();
    }
}
