<?php

namespace IjorTengab\MyFolder\Module\Index\Asset;

class IndexClassJs
{
    public function __toString()
    {
        ob_start(); include('assets/index/index-class.js'); return ob_get_clean();
    }
}
