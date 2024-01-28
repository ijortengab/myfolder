<?php

namespace IjorTengab\MyFolder\Module\Markdown\Asset;

use IjorTengab\MyFolder\Core\Application;

class Style
{
    public function __toString()
    {
        return file_get_contents(Application::$cwd.'/assets/markdown/style.css');
    }
}
