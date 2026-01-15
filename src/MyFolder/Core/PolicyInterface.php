<?php

namespace IjorTengab\MyFolder\Core;

interface PolicyInterface
{
    public function __toString();
    public function setScope($scope);
    public function setOperation($operation);
    public function accessResult();
}
