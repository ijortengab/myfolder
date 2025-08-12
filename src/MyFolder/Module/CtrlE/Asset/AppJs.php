<?php

namespace IjorTengab\MyFolder\Module\CtrlE\Asset;

use IjorTengab\MyFolder\Core\Application;

class AppJs
{
    public function __toString()
    {
        return file_get_contents(Application::$cwd.'/assets/ctrl-e/app.js');
    }
}
