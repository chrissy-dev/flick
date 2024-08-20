<?php

namespace App\Controllers;

use App\Models\Album;
use App\Services\PasswordGuard;
use Jenssegers\Blade\Blade;

session_start();

/**
 * Class AlbumController
 *
 * This controller handles album-related actions such as displaying all albums,
 * showing individual albums, and managing password-protected albums.
 *
 * @package App\Controllers
 */
class AlbumController
{
    /**
     * The Blade template engine instance.
     *
     * @var Blade
     */
    protected $blade;

    /**
     * The PasswordGuard service for managing password attempts and lockouts.
     *
     * @var PasswordGuard
     */
    protected $passwordGuard;

    /**
     * AlbumController constructor.
     *
     * Initializes the Blade template engine and PasswordGuard service.
     */
    public function __construct()
    {
        $this->blade = new Blade('../themes/' .  $_ENV['THEME'], '../cache');
        $this->passwordGuard = new PasswordGuard();
    }

    /**
     * Display a listing of all albums.
     *
     * This method fetches all albums from the model and renders the index view.
     */
    public function index()
    {
        $albums = Album::getAll();
        echo $this->blade->render('index', ['albums' => $albums]);
    }

    /**
     * Display a specific album.
     *
     * This method handles password protection for albums that have a secret.
     * If the correct password is provided, the album's photos are displayed.
     * Otherwise, it renders the password prompt or error messages as needed.
     *
     * @param string $albumName The name of the album to display.
     */
    public function show($albumName)
    {
        $album = new Album($albumName);
        $metadata = $album->getMetadata();

        // Check if the album has a secret
        if (isset($metadata['secret'])) {

            // Check if the user is locked out
            if ($this->passwordGuard->isLockedOut()) {
                echo $this->blade->render('password', ['error' => 'Too many attempts. Please try again later.', 'albumName' => $albumName]);
                return;
            }

            // If a password is submitted, verify it
            if ($_POST['password'] ?? false) {
                if (!$this->passwordGuard->verifyPassword($_POST['password'], $metadata['secret'])) {
                    // Password is incorrect, increment the attempt counter
                    $this->passwordGuard->incrementAttempts();
                    echo $this->blade->render('password', ['error' => 'Incorrect password', 'albumName' => $albumName]);
                    return;
                } else {
                    // Reset attempts on successful login
                    $this->passwordGuard->resetAttempts();
                }
            } else {
                // Prompt for password
                echo $this->blade->render('password', ['albumName' => $albumName]);
                return;
            }
        }

        // Retrieve and render album photos with EXIF data
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

        echo $this->blade->render('album', ['album' => $metadata, 'photos' => $photosWithExif]);
    }
}
