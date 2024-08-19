<?php

namespace App\Models;

use Spatie\YamlFrontMatter\YamlFrontMatter;

class Album
{
    protected $albumName;
    protected $albumPath;

    public function __construct($albumName)
    {
        $this->albumName = $albumName;
        $this->albumPath = '../albums/' . $albumName;
    }

    public static function getAll()
    {
        $albums = array_filter(glob('../albums/*'), 'is_dir');

        $albumData = [];

        foreach ($albums as $albumPath) {
            $albumName = basename($albumPath);
            $metadata = self::getMetadataFromPath($albumPath);

            $albumData[] = [
                'name' => $albumName,
                'title' => $metadata['title'] ?? $albumName, 
                'date' => $metadata['date'] ?? null,
                'cover' => isset($metadata['data']['cover']) ? self::getThumbnail($albumName, $albumPath . '/' . $metadata['data']['cover']): null,
                'data' => $metadata['data'] ?? [],
            ];
        }

        return (object) $albumData;
    }

    protected static function getMetadataFromPath($albumPath)
    {
        $metaFile = $albumPath . '/meta.md';
        if (!file_exists($metaFile)) return null;

        $content = file_get_contents($metaFile);
        $document = YamlFrontMatter::parse($content);
        $frontMatterData = $document->matter(); // Returns an associative array of front matter

        return [
            'title' => $document->title,
            'date' => $document->date,
            'secret' => $document->secret ?? null,
            'data' => $frontMatterData,
            'content' => $document->body()
        ];
    }

    public function getPhotos()
    {
        return glob($this->albumPath . '/*.{jpg,jpeg,png,gif}', GLOB_BRACE);
    }

    public function getMetadata()
    {
        $metaFile = $this->albumPath . '/meta.md';
        if (!file_exists($metaFile)) return null;

        $content = file_get_contents($metaFile);
        $document = YamlFrontMatter::parse($content);
        $frontMatterData = $document->matter(); // Returns an associative array of front matter

        return (object) [
            'title' => $document->title,
            'date' => $document->date,
            'secret' => $document->secret ?? null,
            'data' => $frontMatterData,
            'content' => $document->body()
        ];
    }

    public static function getExifData($photo)
    {
        $exif = @exif_read_data($photo);
        return $exif;
    }

    public static function getResizedPhoto($album, $photo)
    {
        $filename = basename($photo);

        // Generate a unique hash for the filename
        $hash = md5($album . $filename);  // Hashing based on album name and filename

        // Extract the file extension
        $extension = pathinfo($filename, PATHINFO_EXTENSION);

        // Define the cache path and file path using the hashed filename
        $cachePath = "../public/images/$album/$hash.$extension";
        $filePath = "/images/$album/$hash.$extension";

        // Check if the resized image already exists in the cache
        if (!file_exists($cachePath)) {
            // Create the directory if it doesn't exist
            if (!is_dir("../public/images/$album")) {
                mkdir("../public/images/$album", 0755, true);
            }

            // Resize and save the image in the cache
            self::resizeImage($photo, $cachePath);
        }

        // Return the file path for the resized image
        return $filePath;
    }

    public static function getThumbnail($album, $photo, $width = 200, $height = 200)
    {
        $filename = basename($photo);

        // Generate a unique hash for the filename
        $hash = md5($album . $filename);  // Hashing based on album name and filename

        // Extract the file extension
        $extension = pathinfo($filename, PATHINFO_EXTENSION);

        // Define the cache path and file path using the hashed filename
        $cachePath = "../public/images/$album/thumbnails/$hash.$extension";
        $filePath = "/images/$album/thumbnails/$hash.$extension";

        // Check if the resized image already exists in the cache
        if (!file_exists($cachePath)) {
            // Create the directory if it doesn't exist
            if (!is_dir("../public/images/$album/thumbnails")) {
                mkdir("../public/images/$album/thumbnails", 0755, true);
            }

            // Resize and save the image in the cache
            self::resizeImage($photo, $cachePath, $width, $height);
        }

        // Return the file path for the resized image
        return $filePath;
    }


    private static function resizeImage($source, $destination, $maxWidth = 800, $maxHeight = 600)
    {
        list($width, $height, $type) = getimagesize($source);

        $ratio = min($maxWidth / $width, $maxHeight / $height);

        $newWidth = intval($width * $ratio);
        $newHeight = intval($height * $ratio);

        $newImage = imagecreatetruecolor($newWidth, $newHeight);

        switch ($type) {
            case IMAGETYPE_JPEG:
                $sourceImage = imagecreatefromjpeg($source);
                break;
            case IMAGETYPE_PNG:
                $sourceImage = imagecreatefrompng($source);
                break;
            case IMAGETYPE_GIF:
                $sourceImage = imagecreatefromgif($source);
                break;
            default:
                return false;
        }

        imagecopyresampled($newImage, $sourceImage, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

        switch ($type) {
            case IMAGETYPE_JPEG:
                imagejpeg($newImage, $destination, 90);
                break;
            case IMAGETYPE_PNG:
                imagepng($newImage, $destination, 9);
                break;
            case IMAGETYPE_GIF:
                imagegif($newImage, $destination);
                break;
        }

        imagedestroy($newImage);
        imagedestroy($sourceImage);

        return true;
    }
}
