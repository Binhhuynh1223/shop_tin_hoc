<?php

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

require_once __DIR__ . '/../bootstrap.php';

define('APPNAME', 'Shop Tin Học');

session_start();


require_once __DIR__ . '/../vendor/autoload.php';

use Bramus\Router\Router;

$router = new Router();


require_once __DIR__ . '/router/authRouter.php';
require_once __DIR__ . '/router/managerRouter.php';
require_once __DIR__ . '/router/viewRouter.php';


$router->run();
