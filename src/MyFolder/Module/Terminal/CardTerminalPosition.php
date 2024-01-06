<?php

namespace IjorTengab\MyFolder\Module\Terminal;

use IjorTengab\MyFolder\Module\Index\CardInterface;

class CardTerminalPosition implements CardInterface
{
    public function getPlaceholders()
    {
        return new Template\CardTerminalPositionPlaceholder;
    }
    public function getRoute()
    {
        return '/terminal/dashboard/position';
    }
}
