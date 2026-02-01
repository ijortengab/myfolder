<?php

namespace IjorTengab\MyFolder\Module\User\Template;

class UserLoginFormBody
{
    public function __toString()
    {
        ob_start(); include('templates/user/user-login-form-body.html.twig'); return ob_get_clean();
    }
}
