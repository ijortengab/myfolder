<?php

namespace IjorTengab\MyFolder\Module\Index\Asset;

class DateFormat {
    public function __toString()
    {
        return file_get_contents(getcwd().'/assets/index/date-format.js');
    }
}
