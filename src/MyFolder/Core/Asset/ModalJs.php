<?php

namespace IjorTengab\MyFolder\Core\Asset;

class ModalJs
{
    public function __toString()
    {
        ob_start(); include('assets/core/modal.js'); return ob_get_clean();
    }
}
