<?php

namespace IjorTengab\MyFolder\Core\Asset;

class OffcanvasClassJs
{
    public function __toString()
    {
        ob_start(); include('assets/core/offcanvas-class.js'); return ob_get_clean();
    }
}
