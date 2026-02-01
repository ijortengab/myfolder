<?php

namespace IjorTengab\MyFolder\Module\User\Template;

class UserSysAdminCreateConfirmationFormBody
{
    public function __toString()
    {
        ob_start(); include('templates/user/user-sysadmin-create-confirmation-form-body.html.twig'); return ob_get_clean();
    }
}
