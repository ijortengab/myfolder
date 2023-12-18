<?php

namespace IjorTengab\MyFolder\Module\User\Asset;

class ModalCreateUser 
{
    public function __toString()
    {
        return file_get_contents(getcwd().'/assets/user/modal-create-user.js');
    }
}
