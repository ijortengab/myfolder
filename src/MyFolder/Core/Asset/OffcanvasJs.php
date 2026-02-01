<?php

namespace IjorTengab\MyFolder\Core\Asset;

class OffcanvasJs
{
    public function __toString()
    {
        ob_start(); include('assets/core/offcanvas.js'); return ob_get_clean();
    }
}
