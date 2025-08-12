<?php

namespace IjorTengab\MyFolder\Module\IdleBlur\Asset;

use IjorTengab\MyFolder\Core\Application;

class AppJs
{
    public function __toString()
    {
        return file_get_contents(Application::$cwd.'/assets/idle-blur/app.js');
    }
}
