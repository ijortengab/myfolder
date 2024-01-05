<?php

namespace IjorTengab\MyFolder\Module\User\Template;

class UserLoginFormFooter 
{
    public function __toString()
    {
        return file_get_contents(getcwd().'/templates/user/user-login-form-footer.html.twig');
    }
}
