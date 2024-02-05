<?php

namespace IjorTengab\MyFolder\Core;

/**
 * Based on Symfony Response version 2.8.18.
 * https://symfony.com/doc/2.8/components/http_foundation.html#serving-files
 */
class BinaryFileResponse extends Response
{
    protected $info;

    /**
     *
     */
    public function __construct(\SplFileInfo $info)
    {
        $this->info = $info;
    }

    public function send()
    {
        $extension = $this->info->getExtension();
        $path = $this->info->getPathname();
        switch ($extension) {
            case 'css':
                header('Content-Type: text/css');
                break;
            case 'js':
                header('Content-Type: text/javascript');
                break;
            case 'html':
                header('Content-Type: text/html');
                break;
            case 'svg':
                header('Content-Type: image/svg+xml');
                break;
            case 'json':
                header('Content-Type: application/json');
                break;
            default:
                header('Content-Type: ' . mime_content_type($path));
                break;
        }
        if (null === $this->content) {
            readfile($path);
        }
        else {
            echo $this->content;
        }
    }
}
