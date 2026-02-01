<?php

namespace IjorTengab\MyFolder\Module\Markdown\Asset;

class IndexJs
{
    public function __toString()
    {
        ob_start(); include('assets/markdown/index.js'); return ob_get_clean();
    }
}
