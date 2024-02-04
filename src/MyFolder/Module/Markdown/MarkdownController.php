<?php

namespace IjorTengab\MyFolder\Module\Markdown;

use IjorTengab\MyFolder\Core\Application;
use IjorTengab\MyFolder\Core\JsonResponse;
use IjorTengab\MyFolder\Core\ConfigHelper;
use IjorTengab\MyFolder\Core\TwigFile;
use IjorTengab\MyFolder\Core\Response;
use IjorTengab\MyFolder\Core\EventDispatcher;
use IjorTengab\MyFolder\Core\FilePreRenderEvent;
use IjorTengab\MyFolder\Core\HtmlElementEvent;
use IjorTengab\MyFolder\Core\HtmlWrapperHtmlElementEvent;

class MarkdownController
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
        $js = $event->getResources('markdown/js/*');
        $css = $event->getResources('markdown/css/*');

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

        list($base_path, $path_info, $rewrite_url) = Application::extractUrlInfo();
        $settings = array(
            'pathInfo' => $path_info,
            'basePath' => $base_path,
            'rewriteUrl' => $rewrite_url,
        );
        $placeholders = array(
            'js' => array_unique(array_merge($js, $js_1)),
            'css' => array_unique(array_merge($css, $css_1)),
            'settings' => array(
                'global' => json_encode($settings),
                'basePath' => $settings['basePath'],
            ),
        );
        $placeholders['markdown'] = $raw;

        $content = TwigFile::process(new Template\Markdown, $placeholders);
        $response = new Response($content);
        $event->setResponse($response);
    }
}
