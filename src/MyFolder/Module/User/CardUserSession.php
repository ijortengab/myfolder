<?php

namespace IjorTengab\MyFolder\Module\User;

use IjorTengab\MyFolder\Module\Index\CardInterface;

class CardUserSession implements CardInterface
{
    /**
     *
     */
    public function getPlaceholders()
    {
        return new Template\CardUserSessionPlaceholder;
    }

    /**
     *
     */
    public function getRoute()
    {
        return '/user/dashboard/session';
    }
}
