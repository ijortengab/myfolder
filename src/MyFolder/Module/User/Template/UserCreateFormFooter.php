<?php

namespace IjorTengab\MyFolder\Module\User\Template;

class UserCreateFormFooter
{
    public function __toString()
    {
        ob_start(); include('templates/user/user-create-form-footer.html.twig'); return ob_get_clean();
    }
}
