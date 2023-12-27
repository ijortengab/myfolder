<?php

namespace IjorTengab\MyFolder\Module\Index;

use IjorTengab\MyFolder\Core\EventSubscriberInterface;

class HtmlElementSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return array(
            HtmlElementEvent::NAME => 'onHtmlElementEvent',
        );
    }
    public static function onHtmlElementEvent(HtmlElementEvent $event)
    {
        $event->addList('index.dashboard', new Template\NavbarListDashboard, array(
            'label' => 'Dashboard',
            'url' => '/dashboard',
            'offcanvas' => array('name' => 'dashboard'),
        ));

        // External.
        $event->addJs('jquery', 'https://cdn.jsdelivr.net/npm/jquery@3.7.0/dist/jquery.min.js');
        $event->addJs('jquery.once', 'https://cdn.jsdelivr.net/npm/jquery-once@2.2.3/jquery.once.min.js');
        $event->addJs('popper', 'https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js');
        $event->addJs('bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js');
        $event->addCss('bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css');
        $event->addCss('bootstrap-icons', 'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css');
        // Internal
        $event->addJs('local.date-format', '{{ settings.basePath }}/___pseudo/assets/index/date-format.js');
        $event->addJs('local.app', '{{ settings.basePath }}/___pseudo/assets/index/app.js');
        $event->addJs('local.index', '{{ settings.basePath }}/___pseudo/assets/index/index.js');
        $event->addJs('local.ajax', '{{ settings.basePath }}/___pseudo/assets/index/ajax.js');
        $event->addJs('local.modal-class', '{{ settings.basePath }}/___pseudo/assets/index/modal-class.js');
        $event->addJs('local.modal', '{{ settings.basePath }}/___pseudo/assets/index/modal.js');
        $event->addJs('local.offcanvas-class', '{{ settings.basePath }}/___pseudo/assets/index/offcanvas-class.js');
        $event->addJs('local.offcanvas', '{{ settings.basePath }}/___pseudo/assets/index/offcanvas.js');
        $event->addJs('local.card', '{{ settings.basePath }}/___pseudo/assets/index/card.js');
    }
}
