<?php

namespace IjorTengab\MyFolder\Core;

class AccessException extends \Exception
{
    protected $response;

    /**
     *
     */
    public function setResponse(Response $response)
    {
        $this->response = $response;
        return $this;
    }

    /**
     *
     */
    public function getResponse()
    {
        return $this->response;
    }
}
