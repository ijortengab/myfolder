<?php

namespace IjorTengab\MyFolder\Module\Csv\Asset;

class AppJs
{
    public function __toString()
    {
        ob_start(); include('assets/csv/app.js'); return ob_get_clean();
    }
}
