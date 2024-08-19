<?php
// Don't delete this file or modify it
// This file is required for the application to work properly

require_once '../vendor/autoload.php';
require_once '../app/Utilities/Helpers.php';

use App\Utilities\Theme;
use App\Utilities\Router;

$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

// Load the configuration
$config = require_once __DIR__ . '/../app/Utilities/Config.php';

// Set as global
$GLOBALS['config'] = $config;

$theme = new Theme($config);
$theme->setup();

Router::route();
