<?php

namespace IjorTengab\MyFolder\Module\Markdown;

use IjorTengab\MyFolder\Core\Application;
use IjorTengab\MyFolder\Core\ConfigHelper;
use IjorTengab\MyFolder\Core\EventSubscriberInterface;
use IjorTengab\MyFolder\Core\HtmlElementEvent;

class HtmlElementSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return array(
            HtmlElementEvent::NAME => array('onHtmlElementEvent', 100),
        );
    }
    public static function onHtmlElementEvent(HtmlElementEvent $event)
    {
        // https://dworthen.github.io/js-yaml-front-matter/js/yamlFront.js
        // https://cdn.jsdelivr.net/npm/markdown-it-imsize@2.0.1/dist/markdown-it-imsize.js
        // https://cdn.jsdelivr.net/npm/markdown-it-toc-done-right@4.2.0/dist/markdownItTocDoneRight.umd.min.js
        // https://cdn.jsdelivr.net/npm/handlebars@4.7.8/dist/handlebars.min.js
        // https://cdn.jsdelivr.net/npm/markdown-it-video@0.6.3/index.min.js
        // https://cdn.jsdelivr.net/npm/@vrcd-community/markdown-it-video@1.1.1/index.js
        // https://vjs.zencdn.net/8.9.0/video.min.js
        // https://vjs.zencdn.net/8.9.0/video-js.css
        $event->registerResource('markdown/js/jquery', 'https://cdn.jsdelivr.net/npm/jquery@3.7.0/dist/jquery.min.js');
        $event->registerResource('markdown/js/js-yaml', 'https://cdn.jsdelivr.net/npm/js-yaml@4.1.0/dist/js-yaml.min.js');
        $event->registerResource('markdown/js/markdown-it', 'https://cdn.jsdelivr.net/npm/markdown-it@14.0.0/dist/markdown-it.js');
        $event->registerResource('markdown/js/markdown-it/sup', 'https://cdn.jsdelivr.net/npm/markdown-it-sup@2.0.0/dist/markdown-it-sup.min.js');
        $event->registerResource('markdown/js/markdown-it/html5-embed', 'https://cdn.jsdelivr.net/npm/markdown-it-html5-embed@1.0.0/dist/markdown-it-html5-embed.min.js');
        $event->registerResource('markdown/js/markdown-it/emoji', 'https://cdn.jsdelivr.net/npm/markdown-it-emoji@3.0.0/dist/markdown-it-emoji.min.js');
        $event->registerResource('markdown/js/markdown-it/task-lists', 'https://cdn.jsdelivr.net/npm/markdown-it-task-lists@2.1.1/dist/markdown-it-task-lists.min.js');
        $event->registerResource('markdown/js/markdown-it/anchor', 'https://cdn.jsdelivr.net/npm/markdown-it-anchor@8.6.7/dist/markdownItAnchor.umd.min.js');
        $event->registerResource('markdown/js/local/app', '/assets/markdown/app.js');
        $event->registerResource('markdown/css/local/style', '/assets/markdown/style.css');
    }
}
