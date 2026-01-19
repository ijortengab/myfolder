<?php

namespace IjorTengab\MyFolder\Core;

class AccessControl
{
    const DEFAULT_DIRECTORY_LISTING = true;

    const DEFAULT_FILE_EDITING = false;

    const RESERVED_CHARACTERS = array('(', ')', '&', '|');

    const CONJUNCTION = '&';

    const DISJUNCTION = '|';

    protected $scope;

    protected $operation;

    protected $statement;

    protected $policies = array();

    protected $conjunction_policies = array();

    protected static $instances = array();

    /**
     * Reference: http://php.net/manual/en/language.oop5.late-static-bindings.php
     */
    public static function load($scope, $operation)
    {
        if (!isset(static::$instances[$scope][$operation])) {
            static::$instances[$scope][$operation] = new static($scope, $operation);
        }
        return static::$instances[$scope][$operation];
    }

    /**
     *
     */
    public function __construct($scope, $operation)
    {
        $this->scope = $scope;
        $this->operation = $operation;
        return $this;
    }

    public function registerPolicy(PolicyInterface $policy)
    {
        $key = (string) $policy;
        return $this->policies[$key] = $policy;
    }

    public function getOperation()
    {
        return $this->operation;
    }

    public function getScope()
    {
        return $this->scope;
    }

    public function getStatement()
    {
        if (null === $this->statement) {
            $this->populateStatement();
        }
        return $this->statement;
    }

    public function setStatement(array $statement)
    {
        // @todo validate.
        return $this->statement = $statement;
    }

    /**
     *
     */
    public function calculate()
    {
        $dispatcher = Application::getEventDispatcher();

        // Tiap module yang implement ConventionalPolicyEvent, bisa memutuskan
        // bergabung atau tidak
        // dengan memperhatikan scope dan operation.
        $event_conventional = new ConventionalPolicyEvent($this);
        $dispatcher->dispatch($event_conventional, ConventionalPolicyEvent::NAME);
        $this->conjunction_policies = $event_conventional->getConjunctionPolicies();

        if (empty($this->conjunction_policies)) {
            // @todo: jika deny based policy maka return false
            //   jika allow based policy, maka return true.
            switch ($this->operation) {
                case 'file_editing':
                    return static::DEFAULT_FILE_EDITING;

                default:
                    return true;
            }
        }

        $event_alternative = new AlternativePolicyEvent($this);
        $dispatcher->dispatch($event_alternative, AlternativePolicyEvent::NAME);

        $event_emergency = new EmergencyPolicyEvent($this);
        $dispatcher->dispatch($event_emergency, EmergencyPolicyEvent::NAME);

        // Jika tidak ada yg mendaftar AlternativePolicyEvent atau EmergencyPolicyEvent.
        // maka property statement akan tetap null.
        $this->populateStatement();

        $statement = implode('', $this->statement);

        $logic = new MathematicalLogic($statement, array($this, 'tokenToBinary'));

        return $logic->prove();
    }

    /**
     *
     */
    public function decision()
    {
        if ($this->calculate() === false) {
            return $this->setForbidden();
        }
    }

    public function tokenToBinary($token)
    {
        return $this->policies[$token]->accessResult() ? '1' : '0';
    }

    /**
     *
     */
    protected function populateStatement()
    {
        if (null === $this->statement) {
            $this->statement = array();
            $statement = $this->conjunction_policies;

            // Menyisipkan karakter "&" pada setiap element.
            $and  = static::CONJUNCTION;
            $array_length =  count($statement);
            for ($i = 0 ; $i < $array_length; $i++) {
                if ($i === 0) {
                    continue;
                }
                array_splice($statement, (($i * 2) -1), 0, $and);
            }
            $this->statement = $statement;
        }
    }

    protected function setForbidden()
    {
        $response = new Response('Access Denied.');
        $response->setStatusCode(403);
        throw (new AccessException)->setResponse($response);
    }
}
