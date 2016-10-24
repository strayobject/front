<?php
declare(strict_types=1);

chdir(dirname(__DIR__));

require_once 'vendor/autoload.php';

use Aerys\{Host, Http1Driver, Http2Driver, Root, Router, Request, Response, function websocket, function root};
use Front\Controller\{HomePageController, PageController, RedirectController};
/**
 * Env
 * @todo add dotenv support
 */
date_default_timezone_set('UTC');

/**
 * Config
 */
const AERYS_OPTIONS = [
    'user' => 'nobody',
    'keepAliveTimeout' => 60,
    'deflateEnable' => true,
];

/**
 * @todo move bootstrap and routing away from this file
 */
/**
 * templating
 */
$twigLoader = new Twig_Loader_Filesystem(__DIR__.'/views/');
$twig = new Twig_Environment($twigLoader, [
    //'cache' => __DIR__.'/var/cache/',
]);

$oldSiteController = new RedirectController(301, 'old.strayobject.co.uk', false, true);
$aboutPageController = new RedirectController(301, 'strayobject.co.uk/me', false, false);

$router = new Router();
$router->route('GET', '/{path:category|tag/.*}', $oldSiteController);
$router->route('GET', '/{path:\d{4}/?.*}', $oldSiteController);
$router->route('GET', '/{path:about}', $aboutPageController);
$router->route('GET', '/{page:me|projects|blog}/?', new PageController($twig));
$router->route('GET', '/', new HomePageController($twig));


/**
 * @todo fetch data for root and hosts from env
 * @var Root
 */
$rootDir = new Root(__DIR__.'/web');
$rootDir->setOption('mimeFile', __DIR__.'/vendor/amphp/aerys/etc/mime');

$securehost = new Host();
$securehost
    ->name('test.strayobject.co.uk')
    ->expose('*', 443)
    ->encrypt('/root/.acme.sh/test.strayobject.co.uk/test.strayobject.co.uk.key', '/root/.acme.sh/test.strayobject.co.uk/fullchain.pem')
    ->use(new Http1Driver())
    ->use($router)
    ->use($rootDir)
;

$host = new Host();
$host
    ->name('test.strayobject.co.uk')
    ->expose('*', 80)
    ->use(new Http1Driver())
    ->use($router)
    ->use($rootDir)
;
