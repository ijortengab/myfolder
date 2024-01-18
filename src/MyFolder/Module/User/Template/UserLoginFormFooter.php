<?php

namespace IjorTengab\MyFolder\Module\User\Template;

use IjorTengab\MyFolder\Core\Application;

class UserLoginFormFooter 
{
    public function __toString()
    {
        return file_get_contents(Application::$cwd.'/templates/user/user-login-form-footer.html.twig');
    }
}
