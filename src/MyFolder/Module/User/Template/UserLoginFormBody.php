<?php

namespace IjorTengab\MyFolder\Module\User\Template;

class UserLoginFormBody {
    public function __toString()
    {
        return file_get_contents(getcwd().'/templates/user/user-login-form-body.html.twig');
    }
}
