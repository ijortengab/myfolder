<?php

namespace IjorTengab\MyFolder\Module\Index;

use IjorTengab\MyFolder\Core\Event;

class HtmlElementEvent extends Event
{
    const NAME = 'html_element.event';

    protected $js = array();
    protected $css = array();
    protected $list = array();

    public function addJs($id, $url)
    {
        if (!array_key_exists($id, $this->js)) {
            $this->js[$id] = $url;
        }
        return $this;
    }
    public function addCss($id, $url)
    {
        if (!array_key_exists($id, $this->css)) {
            $this->css[$id] = $url;
        }
        return $this;
    }
    public function addList($id, $template, $array)
    {
        if (!array_key_exists($id, $this->list)) {
            $this->list[$id] = array($template, $array);
        }
        return $this;
    }
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
    public function getAllJavascript()
    {
        return array_values($this->js);
    }
    public function getAllCascadingStyleSheets()
    {
        return array_values($this->css);
    }
    public function getAllList()
    {
        return array_values($this->list);
    }
}
