<?php

namespace IjorTengab\MyFolder\Core;

/**
 * Based on Symfony Event Dispatcher version 2.8.18.
 * https://symfony.com/doc/2.8/components/event_dispatcher.html
 */
class EventDispatcher
{
    protected $storage = array();
    public function addSubscriber(EventSubscriberInterface $subscriber)
    {
        foreach ($subscriber->getSubscribedEvents() as $event_name => $method) {
            $this->storage[$event_name][] = array($subscriber, $method);
        }
    }
    public function dispatch($event, $event_name)
    {
        if (array_key_exists($event_name, $this->storage)) {
            foreach ($this->storage[$event_name] as $each) {
                call_user_func_array($each, array($event));
            }
        }
    }
}
