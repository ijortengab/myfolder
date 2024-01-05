<?php

namespace IjorTengab\MyFolder\Module\User\Template;

class UserCreateFormFooter
{
    public function __toString()
    {
        return file_get_contents(getcwd().'/templates/user/user-create-form-footer.html.twig');
    }
}
