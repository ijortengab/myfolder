<?php

namespace IjorTengab\MyFolder\Module\Index\Asset;

class Card {
    public function __toString()
    {
        return file_get_contents(getcwd().'/assets/index/card.js');
    }
}
