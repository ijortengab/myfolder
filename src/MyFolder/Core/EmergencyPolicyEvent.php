<?php

namespace IjorTengab\MyFolder\Core;

class EmergencyPolicyEvent extends Event
{
    const NAME = 'core.emergency_policy.event';

    public $list_policies;

    protected $access;

    /**
     *
     */
    public function __construct(AccessControl $access)
    {
        $this->access = $access;
        return $this;
    }

    /**
     * Ability to direct object access control, so the module will be powerfull.
     */
    public function getAccessControl()
    {
        return $this->access;
    }
}
