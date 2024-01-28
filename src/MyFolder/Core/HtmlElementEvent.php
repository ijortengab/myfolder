<?php

namespace IjorTengab\MyFolder\Core;

class HtmlElementEvent extends Event
{
    const NAME = 'html_element.event';

    protected static $instance;
    protected $resources = array();
    protected $templates = array();

    public function registerResource($id, $value)
    {
        return $this->resources[$id] = $value;;
    }

    public function registerTemplate($id, $contents, $placeholders)
    {
        return $this->templates[$id] = array($contents, $placeholders);
    }

    public function getTemplates($filter_id = null)
    {
        if ($filter_id === null) {
            return array_values($this->templates);
        }
        $storage = array();
        foreach ($this->templates as $id => $value) {
            if (fnmatch($filter_id, $id)) {
                list($template, $array) = $value;
                $storage[$id] = TwigFile::process($template, $array);
            }
        }
        return array_values($storage);
    }

    public function getResources($filter_id = null, $filter_value = null)
    {
        if ($filter_id === null) {
            return array_values($this->resources);
        }
        $storage = array();
        foreach ($this->resources as $id => $value) {
            if (fnmatch($filter_id, $id)) {
                if ($filter_value === null) {
                    $storage[$id] = $value;
                }
                elseif (fnmatch($filter_value, $value)) {
                    $storage[$id] = $value;
                }
            }
        }
        // Jika offline_mode, maka prefix http perlu diganti menjadi local.
        list($base_path,,) = Application::extractUrlInfo();
        $offline_mode = (bool) ConfigHelper::load()->offline_mode->value();
        foreach ($storage as $id => &$value) {
            if (fnmatch('/*', $value)) {
                $value = $base_path.'/___pseudo' . $value;
            }
            elseif ($offline_mode && fnmatch('https://*', $value)) {
                $value = str_replace('https://', $base_path.'/___pseudo/root/cdn/', $value);
            }
        }
        return array_values($storage);
    }

}
