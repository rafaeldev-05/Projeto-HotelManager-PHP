<?php

declare(strict_types=1);

require dirname(__DIR__) . '/app/Core/Autoloader.php';
require dirname(__DIR__) . '/app/Core/helpers.php';

use App\Core\Autoloader;
use App\Core\JsonStore;
use App\Core\Router;

Autoloader::register(dirname(__DIR__));

$config = require dirname(__DIR__) . '/config/app.php';
JsonStore::configure($config['database_path']);

$router = new Router();
require dirname(__DIR__) . '/routes/web.php';

$router->dispatch($_SERVER['REQUEST_METHOD'], parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?: '/');
