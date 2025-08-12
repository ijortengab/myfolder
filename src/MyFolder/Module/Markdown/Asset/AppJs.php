<?php

namespace IjorTengab\MyFolder\Module\Markdown\Asset;

use IjorTengab\MyFolder\Core\Application;

class AppJs
{
    public function __toString()
    {
        return file_get_contents(Application::$cwd.'/assets/markdown/app.js');
    }
}
