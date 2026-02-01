<?php

namespace IjorTengab\MyFolder\Module\User\Template;

class CardUserSessionPlaceholder 
{
    public function __toString()
    {
        ob_start(); include('templates/user/card-user-session-placeholder.html.twig'); return ob_get_clean();
    }
}
