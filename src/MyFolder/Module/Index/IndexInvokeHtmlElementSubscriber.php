<?php

namespace IjorTengab\MyFolder\Module\Index;

use IjorTengab\MyFolder\Core\Application;
use IjorTengab\MyFolder\Core\EventSubscriberInterface;

class IndexInvokeHtmlElementSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return array(
            IndexInvokeHtmlElementEvent::NAME => 'onIndexInvokeHtmlElementEvent',
        );
    }
    public static function onIndexInvokeHtmlElementEvent(IndexInvokeHtmlElementEvent $event)
    {
        $event->addList('index.dashboard', new Template\NavbarListDashboard, array(
            'label' => 'Dashboard',
            'url' => '/dashboard',
            'offcanvas' => array('name' => 'dashboard'),
        ));

        // External.
        $event->addJs('cdn_jquery', 'https://cdn.jsdelivr.net/npm/jquery@3.7.0/dist/jquery.min.js');
        $event->addJs('cdn_jquery_once', 'https://cdn.jsdelivr.net/npm/jquery-once@2.2.3/jquery.once.min.js');
        $event->addJs('cdn_popper', 'https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js');
        $event->addJs('cdn_bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js');
        $event->addCss('cdn_bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css');
        $event->addCss('cdn_bootstrap_icons', 'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css');
        // Internal
        list($base_path,,) = Application::extractUrlInfo();
        $event->addJs('local_date_format', $base_path.'/___pseudo/assets/index/date-format.js');
        $event->addJs('local_app', $base_path.'/___pseudo/assets/index/app.js');
        $event->addJs('local_index_class', $base_path.'/___pseudo/assets/index/index-class.js');
        $event->addJs('local_index', $base_path.'/___pseudo/assets/index/index.js');
        $event->addJs('local_ajax_class', $base_path.'/___pseudo/assets/index/ajax-class.js');
        $event->addJs('local_ajax', $base_path.'/___pseudo/assets/index/ajax.js');
        $event->addJs('local_modal_class', $base_path.'/___pseudo/assets/index/modal-class.js');
        $event->addJs('local_modal', $base_path.'/___pseudo/assets/index/modal.js');
        $event->addJs('local_offcanvas_class', $base_path.'/___pseudo/assets/index/offcanvas-class.js');
        $event->addJs('local_offcanvas', $base_path.'/___pseudo/assets/index/offcanvas.js');
    }
}
