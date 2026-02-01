<?php

namespace IjorTengab\MyFolder\Module\User\Template;

class CardUserSession 
{
    public function __toString()
    {
        ob_start(); include('templates/user/card-user-session.html.twig'); return ob_get_clean();
    }
}
