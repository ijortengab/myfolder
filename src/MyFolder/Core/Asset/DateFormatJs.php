<?php

namespace IjorTengab\MyFolder\Core\Asset;

use IjorTengab\MyFolder\Core\Application;

class DateFormatJs
{
    public function __toString()
    {
        return file_get_contents(Application::$cwd.'/assets/core/date-format.js');
    }
}
