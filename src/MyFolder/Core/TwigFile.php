<?php

namespace IjorTengab\MyFolder\Core;

class TwigFile
{
    protected $template;
    protected $placeholders;

    public static function process($template, $placeholders = null)
    {
        $twig = new self($template, $placeholders);
        return $twig;
    }
    public function __construct($template, $placeholders = null)
    {
        $this->template = $template;
        $this->placeholders = $placeholders;
    }
    public function __toString()
    {
        $twig = new Twig((string) $this->template);
        try {
            $twig->render($this->placeholders);
        }
        catch (RuntimeException $e) {
            trigger_error($e->getMessage());
        }
        return (string) $twig;
    }
}
