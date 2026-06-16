<?php

/**
 * SAPA Lampung MVC — Front Controller
 * Semua request diarahkan ke sini melalui .htaccess
 */

define('BASE_PATH', dirname(__DIR__));

require_once BASE_PATH . '/config/app.php';
require_once BASE_PATH . '/config/database.php';
require_once BASE_PATH . '/app/helpers.php';
require_once BASE_PATH . '/app/core/Controller.php';
require_once BASE_PATH . '/app/core/Model.php';
require_once BASE_PATH . '/routes/Router.php';

foreach (glob(BASE_PATH . '/app/models/*.php') as $modelFile) {
    require_once $modelFile;
}

foreach (glob(BASE_PATH . '/app/controllers/*.php') as $controllerFile) {
    require_once $controllerFile;
}

$router = new Router();

require_once BASE_PATH . '/routes/web.php';

$router->dispatch();
