<?php

namespace IjorTengab\MyFolder\Module\Markdown\Template;

use IjorTengab\MyFolder\Core\Application;

class Markdown
{
    public function __toString()
    {
        return file_get_contents(Application::$cwd.'/templates/markdown/markdown.html.twig');
    }
}
