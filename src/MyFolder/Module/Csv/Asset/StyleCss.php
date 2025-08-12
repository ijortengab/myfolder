<?php

namespace IjorTengab\MyFolder\Module\Csv\Asset;

use IjorTengab\MyFolder\Core\Application;

class StyleCss
{
    public function __toString()
    {
        return file_get_contents(Application::$cwd.'/assets/csv/style.css');
    }
}
