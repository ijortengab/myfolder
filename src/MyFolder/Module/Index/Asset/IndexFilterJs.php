<?php

namespace IjorTengab\MyFolder\Module\Index\Asset;

class IndexFilterJs
{
    public function __toString()
    {
        ob_start(); include('assets/index/index-filter.js'); return ob_get_clean();
    }
}
