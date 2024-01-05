<?php

namespace IjorTengab\MyFolder\Module\User\Template;

class CardUserSessionPlaceholder 
{
    public function __toString()
    {
        return file_get_contents(getcwd().'/templates/user/card-user-session-placeholder.html.twig');
    }
}
