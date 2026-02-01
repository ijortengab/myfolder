<?php

namespace IjorTengab\MyFolder\Module\Csv\Template;

class Csv
{
    public function __toString()
    {
        ob_start(); include('templates/csv/csv.html.twig'); return ob_get_clean();
    }
}
