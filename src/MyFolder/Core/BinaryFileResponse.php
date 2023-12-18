<?php

namespace IjorTengab\MyFolder\Core;

// Credit: https://symfony.com/doc/2.8/components/http_foundation.html#serving-files
class BinaryFileResponse extends Response
{
    protected $file;
    public function __construct($file)
    {
        $this->file = $file;
    }
    public function send()
    {
        $pathinfo = pathinfo(basename($this->file));
        $filename = $pathinfo['filename'];
        $extension = array_key_exists('extension', $pathinfo) ? $pathinfo['extension'] : '';
        switch ($extension) {
            case 'css':
                header('Content-Type: text/css');
                break;
            case 'js':
                header('Content-Type: text/javascript');
                break;
            default:
                header('Content-Type: ' . mime_content_type($this->file));
                break;
        }
        readfile($this->file);
    }
}
