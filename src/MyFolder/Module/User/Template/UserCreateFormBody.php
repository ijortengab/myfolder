<?php

namespace IjorTengab\MyFolder\Module\User\Template;

use IjorTengab\MyFolder\Core\Application;

class UserCreateFormBody 
{
    public function __toString()
    {
        return file_get_contents(Application::$cwd.'/templates/user/user-create-form-body.html.twig');
    }
}
