<?php
declare(strict_types=1);

chdir(dirname(__DIR__));

require_once 'vendor/autoload.php';

use Aerys\{Host, Root, Router, Request, Response, function websocket};
use Front\Controller\{PageController, RedirectController};
/**
 * Env
 */
date_default_timezone_set('UTC');

/**
 * Config
 */
const AERYS_OPTIONS = [
    "user" => "nobody",
    "keepAliveTimeout" => 60,
    //"shutdownTimeout" => 300,
    //"deflateMinimumLength" => 0,
];

/**
 * templating
 */
$twigLoader = new Twig_Loader_Filesystem(__DIR__.'/views/');
$twig = new Twig_Environment($twigLoader, [
    //'cache' => __DIR__.'/var/cache/',
]);

$router = new Router();

$router->route('GET', '/{path:category|tag/.*}', new RedirectController(301, 'old.strayobject.co.uk', false));
$router->route('GET', '/{path:\d{4}/?.*}', new RedirectController(301, 'old.strayobject.co.uk', false));
$router->route('GET', '/{page:about}/?', new PageController($twig));


$rootDir = new Root(__DIR__.'/web');
$host = new Host();
$host
    ->name('localhost')
    //->expose('*', 8899)
    ->expose('*', 8080)
    ->use($router)
    ->use($rootDir)
;
