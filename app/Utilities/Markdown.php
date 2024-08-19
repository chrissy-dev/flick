<?php

namespace App\Utilities;

use Parsedown;

class Markdown
{
    protected $parsedown;

    public function __construct()
    {
        $this->parsedown = new Parsedown();
    }

    /**
     * Parse Markdown content into HTML.
     *
     * @param string $content
     * @return string
     */
    public function parse(string $content): string
    {
        return $this->parsedown->text($content);
    }
}