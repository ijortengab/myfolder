<?php

namespace IjorTengab\MyFolder\Core;

class FilePreRenderEvent extends Event
{
    const NAME = 'core.file_pre_render.event';

    protected static $instance;

    protected $response;

    protected $info;

    public function getInfo()
    {
        return $this->info;
    }

    public function setInfo(\SplFileInfo $info)
    {
        return $this->info = $info;
    }

    public function setResponse(Response $response)
    {
        $this->response = $response;
    }

    public function getResponse()
    {
        return $this->response;
    }
}
