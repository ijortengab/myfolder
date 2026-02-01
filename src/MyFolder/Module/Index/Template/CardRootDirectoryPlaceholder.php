<?php

namespace IjorTengab\MyFolder\Module\Index\Template;

class CardRootDirectoryPlaceholder
{
    public function __toString()
    {
        ob_start(); include('templates/index/card-root-directory-placeholder.html.twig'); return ob_get_clean();
    }
}
