<?php

namespace IjorTengab\MyFolder\Module\Csv;

use IjorTengab\MyFolder\Core\EventSubscriberInterface;
use IjorTengab\MyFolder\Core\HtmlElementEvent;

class HtmlElementSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return array(
            HtmlElementEvent::NAME => array('onHtmlElementEvent'),
        );
    }
    public static function onHtmlElementEvent(HtmlElementEvent $event)
    {
        // https://dworthen.github.io/js-yaml-front-matter/js/yamlFront.js
        // https://cdn.jsdelivr.net/npm/csv-it-imsize@2.0.1/dist/csv-it-imsize.js
        // https://cdn.jsdelivr.net/npm/csv-it-toc-done-right@4.2.0/dist/csvItTocDoneRight.umd.min.js
        // https://cdn.jsdelivr.net/npm/handlebars@4.7.8/dist/handlebars.min.js
        // https://cdn.jsdelivr.net/npm/csv-it-video@0.6.3/index.min.js
        // https://cdn.jsdelivr.net/npm/@vrcd-community/csv-it-video@1.1.1/index.js
        // https://vjs.zencdn.net/8.9.0/video.min.js
        // https://vjs.zencdn.net/8.9.0/video-js.css
        $event->registerResource('csv/js/jquery',                   'https://cdn.jsdelivr.net/npm/jquery@3.7.0/dist/jquery.min.js');
        $event->registerResource('csv/js/jquery-csv',               'https://cdn.jsdelivr.net/npm/jquery-csv@1.0.21/src/jquery.csv.min.js');
        $event->registerResource('csv/js/datatables',               'https://cdn.datatables.net/2.3.5/js/dataTables.min.js');
        $event->registerResource('csv/js/datatables/bootstrap5',    'https://cdn.datatables.net/2.3.5/js/dataTables.bootstrap5.min.js');
        $event->registerResource('csv/js/datatables/absolute',      'https://cdn.datatables.net/plug-ins/2.3.5/sorting/absolute.min.js');
        $event->registerResource('csv/js/datatables/fixedheader',   'https://cdn.datatables.net/fixedheader/4.0.5/js/dataTables.fixedHeader.min.js');
        $event->registerResource('csv/js/datatables/fixedheader/bootstrap5',   'https://cdn.datatables.net/fixedheader/4.0.5/js/fixedHeader.bootstrap5.min.js');
        $event->registerResource('csv/css/bootstrap',               'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css');
        $event->registerResource('csv/css/bootstrap/icons',         'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css');
        $event->registerResource('csv/css/datatables/bootstrap5',   'https://cdn.datatables.net/2.3.5/css/dataTables.bootstrap5.min.css');
        $event->registerResource('csv/css/datatables/fixedheader',  'https://cdn.datatables.net/fixedheader/4.0.5/css/fixedHeader.dataTables.min.css');
        $event->registerResource('csv/css/datatables/fixedheader/bootstrap5',  'https://cdn.datatables.net/fixedheader/4.0.5/css/fixedHeader.bootstrap5.min.css');
        $event->registerResource('csv/js/local/app',                '/assets/csv/app.js');
        $event->registerResource('csv/css/local/style',             '/assets/csv/style.css');
    }
}
