<?php

namespace IjorTengab\MyFolder\Module\User;

use IjorTengab\MyFolder\Core\PolicyInterface;

class IsRegulerUserPolicy implements PolicyInterface
{
    protected $scope;

    protected $operation;

    public function __toString()
    {
        return '[user:is_reguler]';
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
        return UserSession::load()->isAuthenticated();
    }
}
