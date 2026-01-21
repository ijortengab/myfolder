<?php

namespace IjorTengab\MyFolder\Core;

/**
 * Reference: http://php.net/manual/en/language.oop5.late-static-bindings.php
 */
class ConfigReplaceArrayHelper extends ConfigArrayHelper
{
    protected static $cache;

    protected static $cache_storage = array();

    protected static $short_name = 'ConfigReplace';
}
