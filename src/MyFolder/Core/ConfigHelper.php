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
        else {
            if (array_key_exists($module, static::$modules)) {
                return static::$modules[$module];
            }
        }
        $editor = new ConfigEditor;
        if (null === $module) {
            $editor->setClassName('Config', 'IjorTengab\MyFolder\Core');
        }
        else {
            $class = str_replace(' ', '', ucwords(str_replace('_', ' ', $module)));
            $editor->setClassName('Config', 'IjorTengab\\MyFolder\\Module\\'.$class);
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
        else {
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
