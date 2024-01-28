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
        foreach ($subscriber->getSubscribedEvents() as $event_name => $info) {
            // @todo, ganti semua ke array.
            if (is_string($info)) {
                $info = array($info);
            }
            if (!is_array($info)) {
                throw new ModuleException('Event subscriber is not valid.');
            }
            $method = array_shift($info);
            $priority = array_shift($info);
            // Default value is 0.
            $priority = $priority ? $priority : 0;
            $this->storage[$event_name][] = array(
                'handler' => $subscriber,
                'method' => $method,
                'priority' => $priority,
            );
        }
    }
    public function dispatch($event, $event_name)
    {
        if (array_key_exists($event_name, $this->storage)) {
            // @todo, array_column tidak support di php 5.3
            $sorts = array_column($this->storage[$event_name], 'priority');
            array_multisort($sorts, SORT_DESC, $this->storage[$event_name]);
            foreach ($this->storage[$event_name] as $each) {
                call_user_func_array(array($each['handler'], $each['method']), array($event));
            }
        }
    }
}
