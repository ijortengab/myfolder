<?php

namespace IjorTengab\MyFolder\Module\User;

use IjorTengab\MyFolder\Core\EventSubscriberInterface;
use IjorTengab\MyFolder\Core\ConfigHelper;
use IjorTengab\MyFolder\Module\Index\IndexInvokeCommandEvent;

class IndexInvokeCommandSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return array(
            IndexInvokeCommandEvent::NAME => 'onIndexInvokeCommandEvent',
        );
    }
    public static function onIndexInvokeCommandEvent(IndexInvokeCommandEvent $event)
    {
        $config = ConfigHelper::load('user');
        $name = $config->sysadmin->name->value();
        $pass = $config->sysadmin->pass->value();

        // @todo, jika sebelumny ada session, lalu database di edit manual
        // menghapus user.
        // maka kita perlu destroy session.
        $title = 'Logout successful.';
        $body = 'Logout successful.';
        $footer = 'Logout successful.';
        if (empty($name) || empty($pass)) {
            $event->setCommand(array(
                'command' => 'modal',
                'options' => array(
                    'name' => 'dashboard',
                    'bootstrapOptions' => array(
                        'backdrop' => 'static',
                        'keyboard' => true
                    ),
                    'layout' => array(
                        'title' => 'Attention',
                        'body' => 'The account of System Administrator has not been created yet.',
                        'footer' => array(
                            'button' => array(
                                array(
                                    'text' => 'Not now',
                                    'class' => 'btn-ligth',
                                    'bind' => array(
                                        array(
                                            'event' => 'click',
                                            'component' => 'self',
                                            'method' => 'hide',
                                        ),
                                    ),
                                ),
                                array(
                                    'text' => 'Open dashboard',
                                    'class' => 'btn-primary',
                                    'bind' => array(
                                        array(
                                            'event' => 'click',
                                            'component' => 'self',
                                            'method' => 'hide',
                                        ),
                                        array(
                                            'event' => 'click',
                                            'component' => 'offcanvas',
                                            'method' => 'toggle',
                                            'name' => 'dashboard',
                                        ),
                                    ),
                                ),
                            )
                        ),
                    ),
                ),
            ));
        }
    }
}
