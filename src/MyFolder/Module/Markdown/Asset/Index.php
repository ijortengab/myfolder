<?php

namespace IjorTengab\MyFolder\Module\Markdown\Asset;

use IjorTengab\MyFolder\Core\Application;

class Index
{
    public function __toString()
    {
        return file_get_contents(Application::$cwd.'/assets/markdown/index.js');
    }
}
