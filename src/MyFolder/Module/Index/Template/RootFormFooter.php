<?php

namespace IjorTengab\MyFolder\Module\Index\Template;

class RootFormFooter
{
    public function __toString()
    {
        ob_start(); include('templates/index/root-form-footer.html.twig'); return ob_get_clean();
    }
}
