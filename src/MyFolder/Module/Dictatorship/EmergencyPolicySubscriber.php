<?php

namespace IjorTengab\MyFolder\Module\Dictatorship;

use IjorTengab\MyFolder\Core\Application;
use IjorTengab\MyFolder\Core\EventSubscriberInterface;
use IjorTengab\MyFolder\Core\FilePreRenderEvent;
use IjorTengab\MyFolder\Core\Response;
use IjorTengab\MyFolder\Core\RedirectResponse;
use IjorTengab\MyFolder\Core\BinaryFileResponse;
use IjorTengab\MyFolder\Core\ConfigHelper;
use IjorTengab\MyFolder\Core\ConventionalPolicyEvent;
use IjorTengab\MyFolder\Core\EmergencyPolicyEvent;
use IjorTengab\MyFolder\Core\AccessControl;
use IjorTengab\MyFolder\Module\User\IsSysAdminPolicy;

class EmergencyPolicySubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return array(
            EmergencyPolicyEvent::NAME => 'onEmergencyPolicyEvent',
        );
    }
    public static function onEmergencyPolicyEvent(EmergencyPolicyEvent $event)
    {
        $access_control = $event->getAccessControl();
        $statement = $access_control->getStatement();

        $policy = new IsSysAdminPolicy;
        $policy->setScope($access_control->getScope());
        $policy->setOperation($access_control->getOperation());
        $access_control->registerPolicy($policy);
        $key = (string) $policy;

        foreach ($statement as &$each) {
            if (in_array($each, AccessControl::RESERVED_CHARACTERS)) {
                continue;
            }
            elseif ($each == $key) {
                continue;
            }
            $each = '('.$key.'|'.$each.')';
        }

        $statement = implode('', $statement);

        // Split lagi.
        // Versi simple.
        // $pattern = '/(\(|\)|\&|\|)/';
        // Versi njelimet.
        $pattern = '/('.implode('|',array_map('preg_quote', AccessControl::RESERVED_CHARACTERS)).')/';
        $statement = preg_split($pattern, $statement, 0, PREG_SPLIT_DELIM_CAPTURE|PREG_SPLIT_NO_EMPTY);

        $access_control->setStatement($statement);
    }
}
