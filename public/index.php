<?php

/**
 * SAPA Lampung MVC — Front Controller
 * Semua request diarahkan ke sini melalui .htaccess
 */

define('BASE_PATH', dirname(__DIR__));

require_once BASE_PATH . '/config/app.php';
require_once BASE_PATH . '/config/database.php';
require_once BASE_PATH . '/app/controllers/Controller.php';
require_once BASE_PATH . '/routes/Router.php';

$router = new Router();

require_once BASE_PATH . '/routes/web.php';

$router->dispatch();
