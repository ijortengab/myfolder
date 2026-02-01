<?php

namespace IjorTengab\MyFolder\Module\User\Template;

class UserCreateFormBody 
{
    public function __toString()
    {
        ob_start(); include('templates/user/user-create-form-body.html.twig'); return ob_get_clean();
    }
}
