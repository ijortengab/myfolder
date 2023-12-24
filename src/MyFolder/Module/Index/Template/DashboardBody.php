<?php

namespace IjorTengab\MyFolder\Module\Index\Template;

class DashboardBody {
    public function __toString()
    {
        return file_get_contents(getcwd().'/templates/index/dashboard-body.html.twig');
    }
}
