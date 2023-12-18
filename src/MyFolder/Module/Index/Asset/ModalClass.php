<?php

namespace IjorTengab\MyFolder\Module\Index\Asset;

class ModalClass {
    public function __toString()
    {
        return file_get_contents(getcwd().'/assets/index/modal-class.js');
    }
}
