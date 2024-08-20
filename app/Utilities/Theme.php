<?php

namespace App\Utilities;

/**
 * Class Theme
 *
 * This class is responsible for setting up the theme by ensuring that the theme's CSS file
 * is copied to the public directory if it's not already present or if it is outdated.
 *
 * @package App\Utilities
 */
class Theme
{
    /**
     * The name of the theme.
     *
     * @var string
     */
    private $theme;

    /**
     * The source file path of the theme's CSS file.
     *
     * @var string
     */
    private $sourceFile;

    /**
     * The destination directory where the theme's CSS file should be copied.
     *
     * @var string
     */
    private $destinationDir;

    /**
     * The destination file path of the theme's CSS file in the public directory.
     *
     * @var string
     */
    private $destinationFile;

    /**
     * Theme constructor.
     *
     * Initializes the theme by setting the theme name from the configuration and
     * defining the paths for the source and destination files.
     */
    public function __construct()
    {
        // Set the theme from the config
        $this->theme = env('THEME');

        // Define paths
        $this->sourceFile = "../themes/{$this->theme}/{$this->theme}.css";
        $this->destinationDir = "../public/themes/{$this->theme}";
        $this->destinationFile = "{$this->destinationDir}/{$this->theme}.css";
    }

    /**
     * Set up the theme by copying the theme's CSS file to the public directory.
     *
     * This method ensures that the destination directory exists and checks if the
     * destination file is valid (i.e., exists and is up-to-date). If the destination
     * file is not valid, the source file is copied to the destination.
     */
    public function setup()
    {
        // Ensure destination directory exists
        $this->createDestinationDirectory();

        // Check if the destination file exists and if it's up-to-date
        if (!$this->isDestinationFileValid()) {
            // Copy the source file to the destination
            $this->copyFile();
        }
    }

    /**
     * Create the destination directory if it does not already exist.
     *
     * This method ensures that the directory structure is in place to store the
     * theme's CSS file in the public directory.
     */
    private function createDestinationDirectory()
    {
        if (!is_dir($this->destinationDir)) {
            mkdir($this->destinationDir, 0755, true);
        }
    }

    /**
     * Check if the destination file is valid.
     *
     * A file is considered valid if it exists and is up-to-date with the source file.
     * If the destination file does not exist or the source file is newer, it is considered invalid.
     *
     * @return bool Returns true if the destination file is valid, false otherwise.
     */
    private function isDestinationFileValid()
    {
        if (!file_exists($this->destinationFile)) {
            // File doesn't exist, so it's not valid
            return false;
        }

        // Check if the source file is newer
        return filemtime($this->sourceFile) <= filemtime($this->destinationFile);
    }

    /**
     * Copy the theme's CSS file from the source to the destination.
     *
     * This method copies the theme's CSS file from the source directory to the
     * public directory, ensuring that the public version is up-to-date.
     */
    private function copyFile()
    {
        if (copy($this->sourceFile, $this->destinationFile)) {
            return;
        } else {
            echo "Failed to copy the theme CSS file.\n";
        }
    }
}
