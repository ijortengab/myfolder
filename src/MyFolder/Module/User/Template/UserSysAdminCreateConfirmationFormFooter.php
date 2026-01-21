<?php

namespace IjorTengab\MyFolder\Module\User\Template;

use IjorTengab\MyFolder\Core\Application;

class UserSysAdminCreateConfirmationFormFooter
{
    public function __toString()
    {
        return file_get_contents(Application::$cwd.'/templates/user/user-sysadmin-create-confirmation-form-footer.html.twig');
    }
}
