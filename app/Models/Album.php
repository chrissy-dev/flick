<?php

namespace App\Models;

use Spatie\YamlFrontMatter\YamlFrontMatter;

/**
 * Class Album
 *
 * This class represents an album, handling operations such as retrieving album metadata,
 * fetching photos, and resizing images.
 *
 * @package App\Models
 */
class Album
{
    /**
     * The name of the album.
     *
     * @var string
     */
    protected $albumName;

    /**
     * The file path to the album.
     *
     * @var string
     */
    protected $albumPath;

    /**
     * Album constructor.
     *
     * Initializes an Album instance with the given album name and sets the album path.
     *
     * @param string $albumName The name of the album.
     */
    public function __construct($albumName)
    {
        $this->albumName = $albumName;
        $this->albumPath = '../albums/' . $albumName;
    }

    /**
     * Get a list of all albums with their metadata.
     *
     * This method retrieves all albums and their metadata, including the title and date.
     *
     * @return array An array of albums with metadata.
     */
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
                'date' => $metadata['date'] ?? null
            ];
        }

        return $albumData;
    }

    /**
     * Get metadata from the album path.
     *
     * This method reads the metadata from the `meta.md` file in the album directory.
     *
     * @param string $albumPath The path to the album directory.
     * @return array|null An associative array containing metadata or null if the file does not exist.
     */
    protected static function getMetadataFromPath($albumPath)
    {
        $metaFile = $albumPath . '/meta.md';
        if (!file_exists($metaFile)) return null;

        $content = file_get_contents($metaFile);
        $document = YamlFrontMatter::parse($content);

        return [
            'title' => $document->title,
            'date' => $document->date,
            'secret' => $document->secret ?? null,
            'content' => $document->body()
        ];
    }

    /**
     * Get a list of photos in the album.
     *
     * This method retrieves all photos in the album directory with specified extensions.
     *
     * @return array An array of photo file paths.
     */
    public function getPhotos()
    {
        return glob($this->albumPath . '/*.{jpg,jpeg,png,gif}', GLOB_BRACE);
    }

    /**
     * Get metadata for the album.
     *
     * This method reads the metadata from the `meta.md` file within the album.
     *
     * @return array|null An associative array containing metadata or null if the file does not exist.
     */
    public function getMetadata()
    {
        $metaFile = $this->albumPath . '/meta.md';
        if (!file_exists($metaFile)) return null;

        $content = file_get_contents($metaFile);
        $document = YamlFrontMatter::parse($content);

        return [
            'title' => $document->title,
            'date' => $document->date,
            'secret' => $document->secret ?? null,
            'content' => $document->body()
        ];
    }

    /**
     * Get the EXIF data for a photo.
     *
     * This method retrieves EXIF metadata from the specified photo file.
     *
     * @param string $photo The file path to the photo.
     * @return array|false The EXIF data as an associative array, or false if no data is found.
     */
    public static function getExifData($photo)
    {
        $exif = @exif_read_data($photo);
        return $exif;
    }

    /**
     * Get the resized version of a photo.
     *
     * This method generates a resized image and stores it in the cache directory.
     * If the resized image already exists, it returns the cached version.
     *
     * @param string $album The name of the album.
     * @param string $photo The file path to the photo.
     * @return string The file path to the resized photo.
     */
    public static function getResizedPhoto($album, $photo)
    {
        $filename = basename($photo);

        // Generate a unique hash for the filename
        $hash = md5($album . $filename);

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

    /**
     * Resize an image and save it to the specified destination.
     *
     * This method resizes the image to the specified dimensions, maintaining aspect ratio,
     * and saves it in the provided destination path.
     *
     * @param string $source The source file path of the image.
     * @param string $destination The destination file path for the resized image.
     * @param int $maxWidth The maximum width of the resized image. Default is 800 pixels.
     * @param int $maxHeight The maximum height of the resized image. Default is 600 pixels.
     * @return bool Returns true on success, false on failure.
     */
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
