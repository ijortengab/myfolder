<?php

namespace IjorTengab\MyFolder\Core;

// Credit: https://symfony.com/doc/2.8/components/http_foundation.html#redirecting-the-user
class RedirectResponse extends Response {
    protected $targetUrl;
    public function __construct($url)
    {
        $this->targetUrl = $url;
    }
    public function send()
    {
        header('Location: ' . $this->targetUrl);
    }
}
