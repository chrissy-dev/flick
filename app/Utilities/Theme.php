<?php
namespace App\Utilities;

class Theme
{
    private $theme;
    private $sourceFile;
    private $destinationDir;
    private $destinationFile;

    public function __construct()
    {
        // Set the theme from the config
        $this->theme = $GLOBALS['config']['theme'];

        // Define paths
        $this->sourceFile = "../themes/{$this->theme}/{$this->theme}.css";
        $this->destinationDir = "../public/themes/{$this->theme}";
        $this->destinationFile = "{$this->destinationDir}/{$this->theme}.css";
    }

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

    private function createDestinationDirectory()
    {
        if (!is_dir($this->destinationDir)) {
            mkdir($this->destinationDir, 0755, true);
        }
    }

    private function isDestinationFileValid()
    {
        if (!file_exists($this->destinationFile)) {
            // File doesn't exist, so it's not valid
            return false;
        }

        // Check if the source file is newer
        return filemtime($this->sourceFile) <= filemtime($this->destinationFile);
    }

    private function copyFile()
    {
        if (copy($this->sourceFile, $this->destinationFile)) {
            return;
        } else {
            echo "Failed to copy the theme CSS file.\n";
        }
    }
}