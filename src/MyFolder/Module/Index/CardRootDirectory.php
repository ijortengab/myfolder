<?php

namespace IjorTengab\MyFolder\Module\Index;

class CardRootDirectory implements CardInterface
{
    public function getPlaceholders()
    {
        return new Template\CardRootDirectoryPlaceholder;
    }
    public function getRoute()
    {
        return '/index/dashboard/root';
    }
}
