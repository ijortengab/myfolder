<?php

namespace IjorTengab\MyFolder\Module\User\Template;

use IjorTengab\MyFolder\Core\Application;

class CardUserSession 
{
    public function __toString()
    {
        return file_get_contents(Application::$cwd.'/templates/user/card-user-session.html.twig');
    }
}
