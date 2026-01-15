<?php

namespace IjorTengab\MyFolder\Module\User;

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
        $scope = $event->getScope();

        // @todo
        // user bisa nyetting direktori mana yang merupakan
        // admin, sehingga, nanti jika direktori itu, maka kasih policy isSysAdmin.
        // untuk sementara kita nggap aja /admin.
        if (fnmatch('/admin*', $scope)) {
            $event->appendPolicy(new IsSysAdminPolicy);
        }
        // SysAdmin bisa mengedit file dengan melakukan operasi method POST.
        $operation = $event->getOperation();
        switch ($operation) {
            case 'file_editing':
                $event->appendPolicy(new IsSysAdminPolicy);
                break;
        }
    }
}
