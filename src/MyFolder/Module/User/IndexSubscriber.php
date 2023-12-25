<?php

namespace IjorTengab\MyFolder\Module\User;

use IjorTengab\MyFolder\Core\EventSubscriberInterface;
use IjorTengab\MyFolder\Core\Config;
use IjorTengab\MyFolder\Core\ConfigEditor;
use IjorTengab\MyFolder\Module\Index\IndexEvent;

class IndexSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return array(
            IndexEvent::NAME => 'onIndexEvent',
        );
    }
    public static function onIndexEvent(IndexEvent $event)
    {
        $editor = new ConfigEditor;
        $editor->setClassName('Config', 'IjorTengab\MyFolder\Module\User');
        $config = new Config;
        $config->parse($editor->get());
        $name = $config->sysadmin->name->value();
        $pass = $config->sysadmin->pass->value();
        if (empty($pass)) {
            $event->setCommand(array(
                'command' => 'modal',
                'options' => array(
                    'name' => 'sysadminCreateAccountForm',
                    'bootstrapOptions' => array(
                        'backdrop' => 'static',
                        'keyboard' => false
                    ),
                    'layout' => array(
                        'fetch' => '/user/create?part[]=body&part[]=footer',
                        'title' => 'Create Account',
                        'body' => 'Loading...',
                        'footer' => '',
                    ),
                ),
            ));
            // $event->setCommand(array(
                // 'command' => 'fetch',
                // 'options' => array(
                    // 'url' => '/user/create',
                // ),
            // ));
        }
    }
}
