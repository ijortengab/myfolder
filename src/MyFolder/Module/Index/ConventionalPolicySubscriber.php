<?php

namespace IjorTengab\MyFolder\Module\Index;

use IjorTengab\MyFolder\Core\EventSubscriberInterface;
use IjorTengab\MyFolder\Core\ConventionalPolicyEvent;

class ConventionalPolicySubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return array(
            ConventionalPolicyEvent::NAME => 'onConventionalPolicyEvent',
        );
    }
    public static function onConventionalPolicyEvent(ConventionalPolicyEvent $event)
    {
        $operation = $event->getOperation();
        switch ($operation) {
            case 'listing_directory':
            case 'listing_directory_metadata':
                $event->appendPolicy(new FollowParentAcessPolicy);
                break;
        }
    }
}
