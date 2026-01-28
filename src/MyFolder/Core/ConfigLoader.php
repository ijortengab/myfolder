<?php

namespace IjorTengab\MyFolder\Core;

class ConfigLoader
{
    protected static $cache;

    protected static $cache_storage = array();

    protected static $core_added = false;

    protected static $modules_added = array();

    /**
     *
     */
    public static function core()
    {
        return static::load();
    }

    /**
     *
     */
    public static function module($module_name)
    {
        return static::load($module_name);
    }

    /**
     *
     */
    public static function isConfigReplaceValid($path)
    {
        if (filesize($path) === 0) {
            return true;
        }
        $list = token_get_all(file_get_contents($path));
        // Verifikasi baris pertama. harus T_OPEN_TAG = 389
        $first = array_shift($list);
        if (is_array($first)) {
            list($code,,) = $first;
            if ($code === T_OPEN_TAG) {
                return true;
            }
        }

        return false;
    }
    /**
     *
     */
    protected static function load($module_name = null)
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
        $config = ConfigArrayHelper::load($module_name);
        // Try to load the config-replace.php
        try {
            $config_helper = ConfigReplaceArrayHelper::load($module_name);
            $config_array = $config->dump();
            $config_helper_array = $config_helper->dump();
            $array = array_replace_recursive($config_array, $config_helper_array);
            $config_helper->restore($array);
            // Switch config to config_helper.
            $config = $config_helper;
        }
        catch (WriteException $e) {
            $config_replace_php = Application::$cwd.'/'.ConfigReplaceTemplate::BASENAME;
            if (file_exists($config_replace_php)) {
                static::appendTemplateConfigReplace($config_replace_php, $module_name);
            }
        }
        // Save to static.
        if (null === $module_name) {
            static::$cache = $config;
        }
        else {
            static::$cache_storage[$module_name] = $config;
        }
        return $config;
    }

    /**
     *
     */
    protected static function appendTemplateConfigReplace($path, $module_name)
    {
        if (!static::isConfigReplaceValid($path)) {
            // @todo kasih warning.
            return;
        }
        // Cegah double modify.
        if (null === $module_name) {
            if (true === static::$core_added) {
                return;
            }
            static::$core_added = true;
            // @todo: start lock here.
        }
        elseif (is_string($module_name)) {
            if (array_key_exists($module_name, static::$modules_added)) {
                return;
            }
            static::$modules_added[$module_name] = true;
            // @todo: start lock here.
        }

        if (null === $module_name) {
            $string = ConfigReplaceTemplate::TEMPLATE_CORE;
        }
        else {
            $module_name = str_replace(' ', '', ucwords(str_replace('_', ' ', $module_name)));
            $string = ConfigReplaceTemplate::TEMPLATE_MODULE;
            $string = str_replace('$module_name', $module_name, $string);
        }
        if (filesize($path) === 0) {
            $string = '<?php'.PHP_EOL.$string;
        }
        // Karena autoload menggunakan require_once, maka hasil dari class_exists
        // akan tetap false, karena script tidak bisa diload lagi.
        file_put_contents($path, $string, FILE_APPEND);
        opcache_invalidate($path);
        clearstatcache($path);
        // todo perlu flock
    }
}
