<?php

namespace IjorTengab\MyFolder\Core\Asset;

use IjorTengab\MyFolder\Core\Application;

class AjaxClassJs
{
    public function __toString()
    {
        return file_get_contents(Application::$cwd.'/assets/core/ajax-class.js');
    }
}
