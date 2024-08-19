<?php

namespace App\Controllers;

use App\Models\Album;
use Jenssegers\Blade\Blade;

session_start();

class AlbumController
{
    protected $blade;
    protected $themeSettings;

    public function __construct()
    {
        $this->blade = new Blade('../themes/' .  $_ENV['THEME'], '../cache');

        if (file_exists('../themes/' . $_ENV['THEME'] . '/theme-settings.php')) {
            $this->themeSettings = require '../themes/' . $_ENV['THEME'] . '/theme-settings.php';
            $this->themeSettings = (object) $this->themeSettings;
        } else {
            $this->themeSettings = (object) [];
        }
    }

    public function index()
    {  
        $albums = Album::getAll();
        echo $this->blade->render('index', ['albums' => $albums, 'theme' => $this->themeSettings]);
    }

    public function show($albumName)
    {
        $album = new Album($albumName);
        $metadata = $album->getMetadata();

        // Check if the album has a secret
        if (isset($metadata['secret'])) {
            // Initialize the session variables if not set
            if (!isset($_SESSION['attempts'])) {
                $_SESSION['attempts'] = 0;
            }
            if (!isset($_SESSION['last_attempt_time'])) {
                $_SESSION['last_attempt_time'] = time();
            }

            // Set the rate limit parameters
            $maxAttempts = 2; // Max attempts before lockout
            $lockoutTime = 15 * 60; // Lockout time in seconds (15 minutes)

            // Check if the user is locked out
            if ($_SESSION['attempts'] >= $maxAttempts) {
                $timeSinceLastAttempt = time() - $_SESSION['last_attempt_time'];

                if ($timeSinceLastAttempt < $lockoutTime) {
                    // User is locked out
                    echo $this->blade->render('password', ['error' => 'Too many attempts. Please try again later.', 'albumName' => $albumName]);
                    return;
                } else {
                    // Reset attempts after lockout time has passed
                    $_SESSION['attempts'] = 0;
                }
            }

            // If a password is submitted, verify it
            if ($_POST['password'] ?? false) {
                if ($_POST['password'] !== $metadata['secret']) {
                    // Password is incorrect, increment the attempt counter
                    $_SESSION['attempts']++;
                    $_SESSION['last_attempt_time'] = time();

                    echo $this->blade->render('password', ['error' => 'Incorrect password', 'albumName' => $albumName]);
                    return;
                } else {
                    // Reset attempts on successful login
                    $_SESSION['attempts'] = 0;
                }
            } else {
                // Prompt for password
                echo $this->blade->render('password', ['albumName' => $albumName]);
                return;
            }
        }

        $photos = $album->getPhotos();
        $photosWithExif = array_map(function ($photo) use ($albumName) {
            $resizedPhoto = Album::getResizedPhoto($albumName, $photo);
            $exif = Album::getExifData($photo);

            return (object) [
                'path' => $resizedPhoto,
                'caption' => $exif['FileName'],
                'date' => $exif['FileDateTime'],
                'width' => $exif['COMPUTED']['Width'],
                'height' => $exif['COMPUTED']['Height'],
            ];
        }, $photos);

        echo $this->blade->render('album', ['album' => $metadata, 'photos' => $photosWithExif, 'theme' => $this->themeSettings]);
    }
}
