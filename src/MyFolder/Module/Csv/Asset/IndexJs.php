<?php

namespace IjorTengab\MyFolder\Module\Csv\Asset;

class IndexJs
{
    public function __toString()
    {
        ob_start(); include('assets/csv/index.js'); return ob_get_clean();
    }
}
