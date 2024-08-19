<?php 
namespace App\Utilities;

class Router
{
    public static function route()
    {
        $url = trim($_SERVER['REQUEST_URI'], '/');
        $parts = explode('/', $url);

        if (empty($parts[0])) {
            $controller = new \App\Controllers\AlbumController();
            $controller->index();
        } elseif (isset($parts[0]) && $parts[0] === 'album' && isset($parts[1])) {
            $controller = new \App\Controllers\AlbumController();
            $controller->show($parts[1]);
        } else {
            http_response_code(404);
            echo "Page not found";
        }
    }
}