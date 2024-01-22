<?php

namespace IjorTengab\MyFolder\Module\Index;

use IjorTengab\MyFolder\Core\Event;

class IndexInvokeHtmlElementEvent extends Event
{
    const NAME = 'index.invoke_html_element.event';

    protected $js = array();
    protected $css = array();
    protected $list = array();

    public function addJs($id, $url)
    {
        if (!array_key_exists($id, $this->js)) {
            $this->js[$id] = $url;
        }
        else {
            trigger_error("Cannot add javascript with key $id because it already exists.", E_USER_ERROR);
        }
        return $this;
    }
    public function addCss($id, $url)
    {
        if (!array_key_exists($id, $this->css)) {
            $this->css[$id] = $url;
        }
        else {
            trigger_error("Cannot add css with key $id because it already exists.", E_USER_ERROR);
        }
        return $this;
    }
    public function addList($id, $template, $array)
    {
        if (!array_key_exists($id, $this->list)) {
            $this->list[$id] = array($template, $array);
        }
        else {
            trigger_error("Cannot add list with key $id because it already exists.", E_USER_ERROR);
        }
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
}
