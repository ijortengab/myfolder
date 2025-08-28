<?php

namespace IjorTengab\MyFolder\Core\Asset;

use IjorTengab\MyFolder\Core\Application;

class OffcanvasClassJs
{
    public function __toString()
    {
        return file_get_contents(Application::$cwd.'/assets/core/offcanvas-class.js');
    }
}
