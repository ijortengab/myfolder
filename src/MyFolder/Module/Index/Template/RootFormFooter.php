<?php

namespace IjorTengab\MyFolder\Module\Index\Template;

class RootFormFooter {
    public function __toString()
    {
        return file_get_contents(getcwd().'/templates/index/root-form-footer.html.twig');
    }
}
