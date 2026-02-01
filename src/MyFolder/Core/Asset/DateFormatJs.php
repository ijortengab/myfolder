<?php

namespace IjorTengab\MyFolder\Core\Asset;

class DateFormatJs
{
    public function __toString()
    {
        ob_start(); include('assets/core/date-format.js'); return ob_get_clean();
    }
}
