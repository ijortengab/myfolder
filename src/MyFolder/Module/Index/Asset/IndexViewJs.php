<?php

namespace IjorTengab\MyFolder\Module\Index\Asset;

class IndexViewJs
{
    public function __toString()
    {
        ob_start(); include('assets/index/index-view.js'); return ob_get_clean();
    }
}
