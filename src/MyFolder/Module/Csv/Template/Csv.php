<?php

namespace IjorTengab\MyFolder\Module\Csv\Template;

use IjorTengab\MyFolder\Core\Application;

class Csv
{
    public function __toString()
    {
        return file_get_contents(Application::$cwd.'/templates/csv/csv.html.twig');
    }
}
