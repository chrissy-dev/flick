<?php
namespace App\Utilities;

use Dotenv\Dotenv;

class Assets
{
    protected $env;

    public function __construct()
    {
        // Load environment variables from .env file
        $dotenv = Dotenv::createImmutable(dirname(__DIR__, 2));
        $dotenv->load();

        // Get the ENV variable
        $this->env = $_ENV['ENV'] ?? 'development'; // default to 'dev' if ENV is not set
    }

    /**
     * Get the path to the asset based on the environment.
     *
     * @param string $path
     * @return string
     */
    public function getPath(string $path): string
    {
        if ($this->env === 'production') {
            // In production, assets should be served from the public directory
            return '/themes/'. $_ENV['THEME'] . '/' . $path;
        } else {
            // In development, use the local path directly
            return '../themes/'. $_ENV['THEME'] . '/'  . $path;
        }
    }
}
