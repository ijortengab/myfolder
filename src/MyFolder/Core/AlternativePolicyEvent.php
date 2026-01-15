<?php

namespace IjorTengab\MyFolder\Core;

class AlternativePolicyEvent extends Event
{
    const NAME = 'core.alternative_policy.event';

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

    public function getScope()
    {
        return $this->access->getScope();
    }
    public function getOperation()
    {
        return $this->access->getOperation();
    }
    public function prependPolicy(PolicyInterface $policy)
    {
        $this->access->registerPolicy($policy);
        $key = (string) $policy;
        $policy->setScope($this->access->getScope());
        $policy->setOperation($this->access->getOperation());
        $or  = AccessControl::DISJUNCTION;
        $statement = $this->access->getStatement();
        // Start prepend here.
        $statement = array_merge(array($key, $or ,'('), $statement, array(')'));
        $this->access->setStatement($statement);
    }
}
