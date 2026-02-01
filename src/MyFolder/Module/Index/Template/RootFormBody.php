<?php

namespace IjorTengab\MyFolder\Module\Index\Template;

class RootFormBody
{
    public function __toString()
    {
        ob_start(); include('templates/index/root-form-body.html.twig'); return ob_get_clean();
    }
}
