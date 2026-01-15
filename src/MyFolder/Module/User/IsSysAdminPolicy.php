<?php

namespace IjorTengab\MyFolder\Module\User;

use IjorTengab\MyFolder\Core\PolicyInterface;

class IsSysAdminPolicy implements PolicyInterface
{
    protected $scope;

    protected $operation;

    public function __toString()
    {
        return '[user:is_sysadmin]';
    }

    public function setScope($scope)
    {
        $this->scope = $scope;
    }

    public function setOperation($operation)
    {
        $this->operation = $operation;
    }

    public function accessResult()
    {
        return UserSession::load()->isSysAdmin();
    }
}
