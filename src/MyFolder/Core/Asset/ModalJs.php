<?php

namespace IjorTengab\MyFolder\Core\Asset;

use IjorTengab\MyFolder\Core\Application;

class ModalJs
{
    public function __toString()
    {
        return file_get_contents(Application::$cwd.'/assets/core/modal.js');
    }
}
