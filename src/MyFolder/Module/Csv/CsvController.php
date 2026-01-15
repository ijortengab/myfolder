<?php

namespace IjorTengab\MyFolder\Module\Csv;

use IjorTengab\MyFolder\Core\Application;
use IjorTengab\MyFolder\Core\TwigFile;
use IjorTengab\MyFolder\Core\Response;
use IjorTengab\MyFolder\Core\FilePreRenderEvent;
use IjorTengab\MyFolder\Core\HtmlElementEvent;
use IjorTengab\MyFolder\Core\HtmlWrapperHtmlElementEvent;

class CsvController
{

    public static function route()
    {
        $http_request = Application::getHttpRequest();
        $method = strtolower($http_request->server->get('REQUEST_METHOD'));
        $is_ajax = !(null === $http_request->query->get('is_ajax'));
        $has_query_part = !(null === $http_request->query->get('part'));
        switch ($method) {
            case 'post':
                // self::routePost();
                break;
            case 'get':
                if (!$is_ajax) {
                    // self::routeGet();
                }
                elseif ($has_query_part) {
                    // self::routeGetAjaxPart();
                }
                else {
                    // self::routeGetAjax();
                }
                break;
        }
    }
    public static function onFilePreRenderEvent()
    {
        $dispatcher = Application::getEventDispatcher();
        $event = HtmlElementEvent::load();
        $dispatcher->dispatch($event, HtmlElementEvent::NAME);
        $js = $event->getResources('csv/js/*');
        $css = $event->getResources('csv/css/*');

        // Module yang implement html-wrapper juga kita sedot, gan.
        // Return an instance of HtmlElementEvent.
        $event = HtmlWrapperHtmlElementEvent::load();
        $dispatcher->dispatch($event, HtmlWrapperHtmlElementEvent::NAME);
        $js_1 = $event->getResources('core/js/html-wrapper/*');
        $css_1 = $event->getResources('core/css/html-wrapper/*');

        // Get info file from Event.
        $event = FilePreRenderEvent::load();
        $info = $event->getInfo();
        $size = $info->getSize();
        $raw = ($size > 0) ? $info->openFile('r')->fread($size) : '';
        $placeholders = array(
            'js' => array_unique(array_merge($js, $js_1)),
            'css' => array_unique(array_merge($css, $css_1)),
        );
        $placeholders['csv'] = $raw;

        $content = TwigFile::process(new Template\Csv, $placeholders);
        $response = new Response($content);
        $event->setResponse($response);
    }
}
