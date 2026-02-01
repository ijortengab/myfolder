<?php

namespace IjorTengab\MyFolder\Module\Index\Template;

use IjorTengab\MyFolder\Core\Application;

class Index
{
    public function __toString()
    {
        ob_start();
        include('templates/index/index.html.twig');
        return ob_get_clean();
    }
}
