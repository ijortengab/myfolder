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
        // Get info file from Event.
        $event = FilePreRenderEvent::load();
        $info = $event->getInfo();
        $raw = $info->openFile('r')->fread($info->getSize());
        $result = preg_match('/^(-{3}(?:\n|\r)([\w\W]+?)(?:\n|\r)-{3})?([\w\W]*)*/',$raw,  $matches);
        $placeholders = array(
            'js' => $js,
            'css' => $css,
        );
        if (!empty($matches[1])) {
            $placeholders['markdown'] = substr($raw, strlen($matches[1]));
            $placeholders['front_matter'] = $matches[2];
        }
        else {
            $placeholders['markdown'] = $matches[0];
        }
        $content = TwigFile::process(new Template\Markdown, $placeholders);
        $response = new Response($content);
        $event->setResponse($response);
    }
}
