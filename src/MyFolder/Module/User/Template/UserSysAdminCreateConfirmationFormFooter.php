<?php

namespace IjorTengab\MyFolder\Module\User\Template;

class UserSysAdminCreateConfirmationFormFooter
{
    public function __toString()
    {
        ob_start(); include('templates/user/user-sysadmin-create-confirmation-form-footer.html.twig'); return ob_get_clean();
    }
}
