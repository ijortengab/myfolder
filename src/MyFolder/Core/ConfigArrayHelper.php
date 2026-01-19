<?php

namespace IjorTengab\MyFolder\Core;

class ConfigArrayHelper extends ArrayHelper
{
    protected static $short_name = 'Config';

    protected static $cache;

    protected static $cache_storage = array();

    protected $editor;

    public static function load($module_name = null)
    {
        if (null === $module_name) {
            if (null !== static::$cache) {
                return static::$cache;
            }
        }
        else {
            if (array_key_exists($module_name, static::$cache_storage)) {
                return static::$cache_storage[$module_name];
            }
        }
        if (null === $module_name) {
            $editor = new ConfigEditor;
            $editor->setClassName(static::$short_name, 'IjorTengab\MyFolder\Core');
        }
        else {
            $class = str_replace(' ', '', ucwords(str_replace('_', ' ', $module_name)));
            $editor = new ConfigEditor;
            $editor->setClassName(static::$short_name, 'IjorTengab\\MyFolder\\Module\\'.$class);
        }
        $config = new static;
        $config->setEditor($editor);
        $jq = new JsonQuery($config);
        $jq->import($editor->get());
        unset($jq);
        // Save to static.
        if (null === $module_name) {
            static::$cache = $config;
        }
        else {
            static::$cache_storage[$module_name] = $config;
        }
        return $config;
    }

    public function save()
    {
        // Bring back editor.
        $editor = $this->getEditor();
        $editor->set($this);
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
