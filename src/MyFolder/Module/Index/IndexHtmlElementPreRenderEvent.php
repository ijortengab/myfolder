<?php

namespace IjorTengab\MyFolder\Module\Index;

use IjorTengab\MyFolder\Core\Event;

class IndexHtmlElementPreRenderEvent extends Event
{
    const NAME = 'index.html_element_pre_render.event';

    protected static $instance;

    protected $js = array();
    protected $css = array();
    protected $list = array();

    public function overrideJs($id, $url)
    {
        $this->js[$id] = $url;
        return $this;
    }
    public function overrideCss($id, $url)
    {
        $this->css[$id] = $url;
        return $this;
    }
    public function overrideList($id, $template, $array)
    {
        $this->list[$id] = array($template, $array);
        return $this;
    }
    public function dump()
    {
        return array(
            'css' => $this->css,
            'js' => $this->js,
            'list' => $this->list,
        );
    }
    public function restore($array)
    {
        foreach ($array as $key => $value) {
            $this->{$key} = $value;
        }
    }
}
