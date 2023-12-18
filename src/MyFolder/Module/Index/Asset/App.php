<?php

namespace IjorTengab\MyFolder\Module\Index\Asset;

class App {
    public function __toString()
    {
        return file_get_contents(getcwd().'/assets/index/app.js');
    }
}
