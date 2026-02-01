<?php

namespace IjorTengab\MyFolder\Module\Index\Template;

class CardRootDirectory
{
    public function __toString()
    {
        ob_start(); include('templates/index/card-root-directory.html.twig'); return ob_get_clean();
    }
}
