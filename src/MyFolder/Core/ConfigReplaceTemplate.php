<?php

namespace IjorTengab\MyFolder\Core;

class ConfigReplaceTemplate
{
    const BASENAME = 'config-replace.php';

    const TEMPLATE_CORE = <<<'EOL'

namespace IjorTengab\MyFolder\Core;

/**
 *
 */
final class ConfigReplace {}

EOL;

    const TEMPLATE_MODULE = <<<'EOL'

namespace IjorTengab\MyFolder\Module\$module_name;

/**
 *
 */
final class ConfigReplace {}

EOL;

    protected $module_name;

    /**
     *
     */
    public function __construct($module_name = null)
    {
        $this->module_name = $module_name;
        return $this;
    }

    /**
     *
     */
    public function __toString()
    {
        $string = '<?php'.PHP_EOL;
        $string .= self::TEMPLATE_CORE;
        if (null !== $this->module_name) {
            $module_name = $this->module_name;
            $string .= self::TEMPLATE_MODULE;
            $module_name = str_replace(' ', '', ucwords(str_replace('_', ' ', $module_name)));
            $string = str_replace('$module_name', $module_name, $string);
        }
        return $string;
    }

    /**
     *
     */
    public static function init($module_name = null)
    {
        $config_replace_php = Application::$cwd.'/'.self::BASENAME;
        if (!file_exists($config_replace_php)) {
            $contents = (string) (new self($module_name));
            file_put_contents($config_replace_php, $contents);
            // @todo kalo gagal bikin file, maka throw error.
        }
    }
}
