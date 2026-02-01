<?php

namespace IjorTengab\MyFolder\Module\User\Template;

class UserLoginFormFooter 
{
    public function __toString()
    {
        ob_start(); include('templates/user/user-login-form-footer.html.twig'); return ob_get_clean();
    }
}
