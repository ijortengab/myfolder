<?php

namespace IjorTengab\MyFolder\Core;

/**
 * Based on Symfony Response version 2.8.18.
 * https://symfony.com/doc/2.8/components/http_foundation.html#creating-a-json-response
 */
class JsonResponse extends Response
{
    protected $data;
    public function __construct($data = null)
    {
        $this->data = $data;
    }
    public function setData($data = null)
    {
        $this->data = $data;
        return $this;
    }
    public function send()
    {
        header("Content-Type: application/json");
        echo json_encode($this->data);
    }
}
