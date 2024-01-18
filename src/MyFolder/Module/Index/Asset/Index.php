<?php

namespace IjorTengab\MyFolder\Module\Index\Asset;

use IjorTengab\MyFolder\Core\Application;

class Index
{
    public function __toString()
    {
        return file_get_contents(Application::$cwd.'/assets/index/index.js');
    }
}
