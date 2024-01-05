<?php

namespace IjorTengab\MyFolder\Module\User\Template;

class CardUserSession 
{
    public function __toString()
    {
        return file_get_contents(getcwd().'/templates/user/card-user-session.html.twig');
    }
}
