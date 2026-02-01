<?php

namespace IjorTengab\MyFolder\Module\Index\Asset;

class IndexJs
{
    public function __toString()
    {
        ob_start(); include('assets/index/index.js'); return ob_get_clean();
    }
}
