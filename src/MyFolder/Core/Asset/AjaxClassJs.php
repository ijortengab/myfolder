<?php

namespace IjorTengab\MyFolder\Core\Asset;

class AjaxClassJs
{
    public function __toString()
    {
        ob_start(); include('assets/core/ajax-class.js'); return ob_get_clean();
    }
}
