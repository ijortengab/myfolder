<?php

namespace IjorTengab\MyFolder\Module\User\Template;

class CardUserSysAdminCreatePlaceholder 
{
    public function __toString()
    {
        ob_start(); include('templates/user/card-user-sysadmin-create-placeholder.html.twig'); return ob_get_clean();
    }
}
