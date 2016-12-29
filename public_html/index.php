<?php
if (PHP_SAPI == 'cli-server') {
    // To help the built-in PHP dev server, check if the request was actually for
    // something which should probably be served as a static file
    $url = parse_url($_SERVER['REQUEST_URI']);
    $file = __DIR__ . $url['path'];
    if (is_file($file)) {
        return false;
    }
}

require __DIR__ . '/../vendor/autoload.php';
spl_autoload_register(function ($className) {
    $namespace = str_replace("\\", "/", __NAMESPACE__);
    $className = str_replace("\\", "/", $className);
    $class = __DIR__ . "/../src/$namespace/$className.php";
    /** @noinspection PhpIncludeInspection */
    include_once($class);
});

session_start();

// Instantiate the app
$settings = require __DIR__ . '/../src/Settings.php';
$app = new \Slim\App($settings);

// Set up dependencies
require __DIR__ . '/../src/Dependencies.php';

// Register middleware
require __DIR__ . '/../src/Middleware.php';

// Register routes
require __DIR__ . '/../src/Routes.php';

// Run app
$app->run();
