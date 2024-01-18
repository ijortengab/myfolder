<?php

namespace IjorTengab\MyFolder\Module\User\Template;

use IjorTengab\MyFolder\Core\Application;

class UserLoginFormBody
{
    public function __toString()
    {
        return file_get_contents(Application::$cwd.'/templates/user/user-login-form-body.html.twig');
    }
}
