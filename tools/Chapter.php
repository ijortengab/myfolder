<?php

namespace IjorTengab\MyFolder\Tools;

class Chapter
{
    protected $current_chapter;
    protected $contents;
    protected $total_chapter;

    /**
     *
     */
    public function __construct($total_chapter = null)
    {
        if (is_int($total_chapter)) {
            $this->setTotalChapter($total_chapter);
        }
    }

    /**
     *
     */
    public function setTotalChapter($total_chapter)
    {
        $this->total_chapter = $total_chapter;
        $last = $total_chapter - 1;
        $this->contents = range(0, $last);
        array_walk($this->contents, function (&$value) {
            $value = array();
        });
    }

    /**
     *
     */
    public function inChapter(int $int)
    {
        $this->current_chapter = $int - 1;
        return $this;
    }

    /**
     *
     */
    public function appendThis($mixed)
    {
        if (is_string($mixed)) {
            $this->contents[$this->current_chapter][] = $mixed;
        }
        elseif (is_array($mixed)) {
            $this->contents[$this->current_chapter] = array_merge($this->contents[$this->current_chapter], $mixed);
        }
    }

    public function unique()
    {
        $this->contents[$this->current_chapter] = array_unique($this->contents[$this->current_chapter]);
    }

    /**
     *
     */
    public function __toString()
    {
        $string = '';
        foreach ($this->contents as $chapter) {
            array_walk($chapter, function (&$value) {
                if (is_array($value)) {
                    $value = implode("\n", $value);
                }
                $value .= "\n";
            });
            $string .= implode('', $chapter);
        }
        return $string;
    }
}
