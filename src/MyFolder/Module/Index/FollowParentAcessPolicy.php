<?php

namespace IjorTengab\MyFolder\Module\Index;

use IjorTengab\MyFolder\Core\PolicyInterface;
use IjorTengab\MyFolder\Core\ConfigHelper;
use IjorTengab\MyFolder\Core\AccessControl;
use IjorTengab\MyFolder\Module\Index\AccessControl as IndexAccessControl;

class FollowParentAcessPolicy implements PolicyInterface
{
    protected $scope;

    protected $operation;

    public function __toString()
    {
        return '[index:follow_parent_access]';
    }

    public function setScope($scope)
    {
        $this->scope = $scope;
    }

    public function setOperation($operation)
    {
        $this->operation = $operation;
    }

    /**
     * Wajib return boolean.
     */
    public function accessResult()
    {
        if ($this->scope == '/' ) {
            $config = ConfigHelper::load();
            $access_root_public = $config->access->root->public->value();

            switch ($access_root_public) {
                case '0':
                    $access_root_public = false;
                    break;

                case '1':
                    $access_root_public = true;
                    break;

                default:
                    $access_root_public = null;
                    break;
            }
            if ($access_root_public === null) {
                $access_root_public = IndexAccessControl::DEFAULT_DIRECTORY_LISTING;
            }
            return $access_root_public;
        }
        else {
            // Agar standard, sama seperti yang dilakukan oleh IndexController,
            // maka kita beri trailing slash.
            $parent = dirname($this->scope);
            if ($parent !== '/') {
                $parent .= '/';
            }
            return AccessControl::load($parent, $this->operation)->calculate();
        }
    }
}
