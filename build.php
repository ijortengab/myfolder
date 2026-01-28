<?php

namespace IjorTengab\MyFolder\Tools;

// https://www.php.net/manual/en/language.namespaces.definitionmultiple.php

if (PHP_SAPI !== 'cli') {
    die('CLI only.');
}

require 'vendor/autoload.php';
// Credit: https://github.com/symfony/polyfill-php80/blob/1.x/Php80.php
if (!function_exists('str_starts_with')) {
    function str_starts_with($haystack, $needle) {
        return 0 === strncmp($haystack, $needle, \strlen($needle));
    }
}
if ($argc == 1) {
    die('Usage: php build.php <PATH> [--module=MODULE]...'.PHP_EOL);
}
array_shift($argv);
$path = array_shift($argv);
$modules = array();
foreach ($argv as $key => $value) {
    if (str_starts_with($value,'--module=')) {
        $modules[] = substr($value, strlen('--module='));
    }
}

// ---
function ConfigEditor__toDocComment__stringify(array $array) {
    return PhpScriptMinifiedClass::stringifyMethodContainsHeredoc($array);
}
function ConfigReplaceTemplate__stringify(array $array) {
    return PhpScriptMinifiedClass::stringifyPropertiesContainsHeredoc($array);
}
// ---

$php = new PhpScript($path);
$php->setTotalChapter(4);
$scandir = new ScanDirPhpScript('IjorTengab\\MyFolder\\','src/MyFolder/');

// ============
$namespace = 'IjorTengab\MyFolder\Core';
// ============
$chapter = new Chapter(5);
$chapter->inChapter(1)->appendThis("namespace $namespace {");
// ---
$scandir->setNameSpace($namespace);
$scandir->exclude('Config.php');
foreach ($scandir as $php_file) {
    $path = (string) $php_file;
    $minified = new PhpScriptMinifiedClass($namespace, $path);
    $chapter->inChapter(2)->appendThis($minified->getAlias());
    $chapter->inChapter(3)->appendThis(
        (string) $minified->stripOpeningTag()
                          ->stripNameSpace()
                          ->stripAlias()
    );
}
$chapter->inChapter(2)->unique();
$chapter->inChapter(5)->appendThis('}');
$chapter->inChapter(5)->appendThis('');
// ---
$php->inChapter(3)->appendThis($chapter);
// Config.php
$chapter = new Chapter(5);
$chapter->inChapter(1)->appendThis("namespace $namespace {");
$excluded_expanded =$scandir->getExcludedExpanded();
$php_file = array_shift($excluded_expanded);
$path = (string) $php_file;
$minified = new PhpScriptMinifiedClassSimple($namespace, $path);
$modules_modified = $modules;
array_walk($modules_modified, function (&$value) {
    $value = '.module.'.$value.' 1';
});
$minified->addDocComment($modules_modified);
$chapter->inChapter(2)->appendThis($minified->getAlias());
$chapter->inChapter(3)->appendThis(
    (string) $minified->stripOpeningTag()
                      ->stripNameSpace()
                      ->stripAlias()
);
$chapter->inChapter(2)->unique();
$chapter->inChapter(5)->appendThis('}');
$chapter->inChapter(5)->appendThis('');
// ---
$php->inChapter(2)->appendThis($chapter);

// ============
$namespace = 'IjorTengab\MyFolder\Core\Asset';
// ============
$chapter = new Chapter(5);
$chapter->inChapter(1)->appendThis("namespace $namespace {");
// ---
$scandir->setNameSpace($namespace);
foreach ($scandir as $php_file) {
    $path = (string) $php_file;
    $minified = new PhpScriptMinifiedClassTemplate($namespace, $path);
    $chapter->inChapter(2)->appendThis($minified->getAlias());
    $chapter->inChapter(3)->appendThis(
        (string) $minified->stripOpeningTag()
                          ->stripNameSpace()
                          ->stripAlias()
    );
}
$chapter->inChapter(2)->unique();
$chapter->inChapter(5)->appendThis('}');
$chapter->inChapter(5)->appendThis('');
// ---
$php->inChapter(3)->appendThis($chapter);

// ============
$namespace = 'IjorTengab\MyFolder\Core\Template';
// ============
$chapter = new Chapter(5);
$chapter->inChapter(1)->appendThis("namespace $namespace {");
// ---
$scandir->setNameSpace('IjorTengab\MyFolder\Core\Template');
foreach ($scandir as $php_file) {
    $path = (string) $php_file;
    $minified = new PhpScriptMinifiedClassTemplate($namespace, $path);
    $chapter->inChapter(2)->appendThis($minified->getAlias());
    $chapter->inChapter(3)->appendThis(
        (string) $minified->stripOpeningTag()
                          ->stripNameSpace()
                          ->stripAlias()
    );
}
$chapter->inChapter(2)->unique();
$chapter->inChapter(5)->appendThis('}');
$chapter->inChapter(5)->appendThis('');
// ---
$php->inChapter(3)->appendThis($chapter);

foreach ($modules as $_module) {
    $module = str_replace(' ', '', ucwords(str_replace('_', ' ', $_module)));
    // ============
    $namespace = "IjorTengab\\MyFolder\\Module\\$module";
    // ============
    $chapter = new Chapter(5);
    $chapter->inChapter(1)->appendThis("namespace $namespace {");
    // ---
    $scandir->setNameSpace($namespace);
    $scandir->exclude('Config.php');
    foreach ($scandir as $php_file) {
        $path = (string) $php_file;
        $minified = new PhpScriptMinifiedClass($namespace, $path);
        $chapter->inChapter(2)->appendThis($minified->getAlias());
        $chapter->inChapter(3)->appendThis(
            (string) $minified->stripOpeningTag()
                              ->stripNameSpace()
                              ->stripAlias()
        );
    }
    $chapter->inChapter(2)->unique();
    $chapter->inChapter(5)->appendThis('}');
    $chapter->inChapter(5)->appendThis('');
    // ---
    $php->inChapter(3)->appendThis($chapter);
    // Config.php
    $chapter = new Chapter(5);
    $chapter->inChapter(1)->appendThis("namespace $namespace {");
    foreach ($scandir->getExcludedExpanded() as $php_file) {
        $path = (string) $php_file;
        $minified = new PhpScriptMinifiedClassSimple($namespace, $path);
        $minified->addDocComment();
        $chapter->inChapter(2)->appendThis($minified->getAlias());
        $chapter->inChapter(3)->appendThis(
            (string) $minified->stripOpeningTag()
                              ->stripNameSpace()
                              ->stripAlias()
        );
    }
    $chapter->inChapter(2)->unique();
    $chapter->inChapter(5)->appendThis('}');
    $chapter->inChapter(5)->appendThis('');
    // ---
    $php->inChapter(2)->appendThis($chapter);

    // ============
    $namespace = "IjorTengab\\MyFolder\\Module\\$module\\Asset";
    // ============
    $chapter = new Chapter(5);
    $chapter->inChapter(1)->appendThis("namespace $namespace {");
    // ---
    $scandir->setNameSpace($namespace);
    foreach ($scandir as $php_file) {
        $path = (string) $php_file;
        $minified = new PhpScriptMinifiedClassTemplate($namespace, $path);
        $chapter->inChapter(2)->appendThis($minified->getAlias());
        $chapter->inChapter(3)->appendThis(
            (string) $minified->stripOpeningTag()
                              ->stripNameSpace()
                              ->stripAlias()
        );
    }
    $chapter->inChapter(2)->unique();
    $chapter->inChapter(5)->appendThis('}');
    $chapter->inChapter(5)->appendThis('');
    // ---
    $php->inChapter(3)->appendThis($chapter);

    // ============
    $namespace = "IjorTengab\\MyFolder\\Module\\$module\\Template";
    // ============
    $chapter = new Chapter(5);
    $chapter->inChapter(1)->appendThis("namespace $namespace {");
    // ---
    $scandir->setNameSpace($namespace);
    foreach ($scandir as $php_file) {
        $path = (string) $php_file;
        $minified = new PhpScriptMinifiedClassTemplate($namespace, $path);
        $chapter->inChapter(2)->appendThis($minified->getAlias());
        $chapter->inChapter(3)->appendThis(
            (string) $minified->stripOpeningTag()
                              ->stripNameSpace()
                              ->stripAlias()
        );
    }
    $chapter->inChapter(2)->unique();
    $chapter->inChapter(5)->appendThis('}');
    $chapter->inChapter(5)->appendThis('');
    // ---
    $php->inChapter(3)->appendThis($chapter);
}

// Save.
$php->inChapter(1)->appendThis('<?php');
$php->inChapter(1)->appendThis('');
$minified = new PhpScriptMinified('index.php');
$php->inChapter(4)->appendThis("namespace {");
$php->inChapter(4)->appendThis(
    (string) $minified->stripOpeningTag()
                      ->stripFixedString("require 'vendor/autoload.php';")
);
$php->inChapter(4)->appendThis("}");
$php->inChapter(4)->appendThis('');
$php->save();
