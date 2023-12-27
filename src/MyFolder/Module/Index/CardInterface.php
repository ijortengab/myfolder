<?php

namespace IjorTengab\MyFolder\Module\Index;

use IjorTengab\MyFolder\Core\ModuleInterface;
use IjorTengab\MyFolder\Core\Application;

interface CardInterface
{
    public function getPlaceholders();
    public function getRoute();
}
