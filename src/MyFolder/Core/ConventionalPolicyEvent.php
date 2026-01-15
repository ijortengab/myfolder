<?php

namespace IjorTengab\MyFolder\Core;

class ConventionalPolicyEvent extends Event
{
    const NAME = 'core.conventional_policy.event';

    protected static $instance;

    protected $access;

    protected $conjunction_policies = array();

    /**
     *
     */
    public function __construct(AccessControl $access)
    {
        $this->access = $access;
        return $this;
    }

    public function appendPolicy(PolicyInterface $policy)
    {
        // @todo, validate perlu square bracket.
        $this->access->registerPolicy($policy);
        $policy->setScope($this->access->getScope());
        $policy->setOperation($this->access->getOperation());
        $this->conjunction_policies[] = (string) $policy;
    }

    public function getScope()
    {
        return $this->access->getScope();
    }

    public function getOperation()
    {
        return $this->access->getOperation();
    }

    public function getConjunctionPolicies()
    {
        return $this->conjunction_policies;
    }
}
