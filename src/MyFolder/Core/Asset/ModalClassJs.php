<?php

namespace IjorTengab\MyFolder\Core\Asset;

class ModalClassJs
{
    public function __toString()
    {
        ob_start(); include('assets/core/modal-class.js'); return ob_get_clean();
    }
}
