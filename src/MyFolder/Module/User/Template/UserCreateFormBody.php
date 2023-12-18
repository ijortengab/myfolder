<?php

namespace IjorTengab\MyFolder\Module\User\Template;

class UserCreateFormBody {
    public function __toString()
    {
        return file_get_contents(getcwd().'/templates/user/user-create-form-body.html.twig');
    }
}
