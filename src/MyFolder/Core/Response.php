<?php

namespace IjorTengab\MyFolder\Core;

/**
 * Based on Symfony Response version 2.8.18.
 * https://symfony.com/doc/2.8/components/http_foundation.html#sending-the-response
 */
class Response
{
    protected $content;
    protected $statusCode;

    public function __construct($content = '', $status = 200, $headers = array())
    {
        $this->content = $content;
        $this->statusCode = $status;
    }
    public function setContent($content)
    {
        $this->content = $content;
        return $this;
    }
    public function setStatusCode($code, $text = null)
    {
        $this->statusCode = $code;
    }
    public function send()
    {
        if ($this->statusCode == 404) {
            header('HTTP/1.1 404 Not Found');
        }
        echo $this->content;
    }
}
