<?php

namespace IjorTengab\MyFolder\Core;

class ConfigHelper extends ArrayHelper
{
    protected static $core;

    protected static $modules = array();

    protected $editor;

    public static function load($module = null)
    {
        if (null === $module) {
            if (null !== static::$core) {
                return static::$core;
            }
        }
        elseif (is_string($module)) {
            if (array_key_exists($module, static::$modules)) {
                return static::$modules[$module];
            }
        }
        if (null === $module) {
            $editor = new ConfigEditor;
            $editor->setClassName('Config', 'IjorTengab\MyFolder\Core');
        }
        elseif (is_string($module)) {
            $class = str_replace(' ', '', ucwords(str_replace('_', ' ', $module)));
            $editor = new ConfigEditor;
            $editor->setClassName('Config', 'IjorTengab\\MyFolder\\Module\\'.$class);
        }
        elseif (is_object($module)) {
            $editor = new ConfigEditor($module);
        }
        $config = new self;
        $config->setEditor($editor);
        $jq = new JsonQuery($config);
        $jq->import($editor->get());
        unset($jq);
        // Save to static.
        if (null === $module) {
            static::$core = $config;
        }
        elseif (is_string($module)) {
            static::$modules[$module] = $config;
        }
        return $config;
    }

    public static function save(ConfigHelper $config)
    {
        // Bring back editor.
        $editor = $config->getEditor();
        $editor->set($config);
    }

    public function __toString()
    {
        $jq = new JsonQuery($this);
        return $jq->export();
    }

    public function getEditor()
    {
        return $this->editor;
    }
    public function setEditor(ConfigEditor $editor)
    {
        $this->editor = $editor;
    }

}
