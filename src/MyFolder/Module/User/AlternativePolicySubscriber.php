<?php

namespace IjorTengab\MyFolder\Module\User;

use IjorTengab\MyFolder\Core\EventSubscriberInterface;
use IjorTengab\MyFolder\Core\AlternativePolicyEvent;

class AlternativePolicySubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return array(
            AlternativePolicyEvent::NAME => 'onAlternativePolicyEvent',
        );
    }
    public static function onAlternativePolicyEvent(AlternativePolicyEvent $event)
    {
        $scope = $event->getScope();
        $operation = $event->getOperation();
        switch ($scope) {
            case '/':
                switch ($operation) {
                    case 'listing_directory':
                    case 'listing_directory_metadata':
                        $event->prependPolicy(new IsRegulerUserPolicy);
                        break;
                }
                break;
        }
    }
}
