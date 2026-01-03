<?php

namespace IjorTengab\MyFolder\Module\User;

use IjorTengab\MyFolder\Module\Index\CardInterface;

class CardUserSysAdminCreate implements CardInterface
{
    public function getPlaceholders()
    {
        return new Template\CardUserSysAdminCreatePlaceholder;
    }
    public function getRoute()
    {
        return '/user/sysadmin/create';
    }
}
