<?php
use App\Utilities\Assets;
use App\Utilities\Markdown;

if (!function_exists('asset_path')) {
    /**
     * Get the path to an asset using the Assets helper.
     *
     * @param string $path
     * @return string
     */
    function asset_path($path)
    {
        $assets = new Assets();
        return $assets->getPath($path);
    }
}

if (!function_exists('parse_markdown')) {
    /**
     * Parse Markdown content into HTML using the Markdown helper.
     *
     * @param string $content
     * @return string
     */
    function parse_markdown($content)
    {
        $markdown = new Markdown();
        return $markdown->parse($content);
    }
}

if (!function_exists('dd')) {
    function dd(...$args)
    {
        foreach ($args as $arg) {
            echo '<pre>';
            print_r($arg);
            echo '</pre>';
        }
        die(1);
    }
}