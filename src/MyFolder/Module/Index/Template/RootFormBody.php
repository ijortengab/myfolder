<?php

namespace IjorTengab\MyFolder\Module\Index\Template;

class RootFormBody
{
    public function __toString()
    {
        return file_get_contents(getcwd().'/templates/index/root-form-body.html.twig');
    }
}
