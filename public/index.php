<?php
// Don't delete this file or modify it
// This file is required for the application to work properly

require_once '../vendor/autoload.php';
require_once '../app/Utilities/Helpers.php';

use App\Utilities\Theme;
use App\Utilities\Router;

$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

$theme = new Theme();
$theme->setup();

Router::route();
