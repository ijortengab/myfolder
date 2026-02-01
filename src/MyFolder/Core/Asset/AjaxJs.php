<?php

namespace IjorTengab\MyFolder\Core\Asset;

class AjaxJs
{
    public function __toString()
    {
        ob_start(); include('assets/core/ajax.js'); return ob_get_clean();
    }
}
