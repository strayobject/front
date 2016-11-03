<?php
declare(strict_types=1);

chdir(dirname(__DIR__));

require_once 'vendor/autoload.php';

use Aerys\{Host, Http1Driver, Http2Driver, Root, Router, Request, Response, function websocket, function root};
use Front\Controller\{HomePageController, PageController, RedirectController};
use Front\Feed\{UnifiedFeed, Provider\MediumFeedProvider, Provider\TwitterFeedProvider};
use Abraham\TwitterOAuth\TwitterOAuth;
use PicoFeed\Reader\Reader;
/**
 * Server Config
 */
const AERYS_OPTIONS = [
    'user' => 'nobody',
    'keepAliveTimeout' => 60,
    'deflateEnable' => true,
];
/**
 * Env
 */
date_default_timezone_set('UTC');
/**
 * @todo handle possible lack of .env file
 * @var Dotenv
 */
$dotenv = new Dotenv\Dotenv(__DIR__);
$dotenv->load();
/**
 * @todo move bootstrap and routing away from this file
 */
/**
 * templating
 */
$options = [];

if (file_exists('var/cache') || mkdir('var/cache', 0777, true)) {
    $options['cache'] = __DIR__.'/var/cache/';
}

$twigLoader = new Twig_Loader_Filesystem(__DIR__.'/views/');
$twig = new Twig_Environment($twigLoader, $options);
$twitterFeedProvider = new TwitterFeedProvider(
    new TwitterOAuth(
        getenv('TWITTER_CONSUMER_KEY'),
        getenv('TWITTER_CONSUMER_SECRET'),
        getenv('TWITTER_ACCESS_TOKEN'),
        getenv('TWITTER_ACCESS_TOKEN_SECRET')
    )
);
$mediumFeedProvider = new MediumFeedProvider(new Reader(), getenv('MEDIUM_FEED_URL'));
$homeFeed = new UnifiedFeed($mediumFeedProvider, $twitterFeedProvider);
$blogFeed = new UnifiedFeed($mediumFeedProvider);
$oldSiteController = new RedirectController(301, 'oldblog.strayobject.co.uk', false, true);
$aboutPageController = new RedirectController(301, 'strayobject.co.uk/me', false, false);
$router = new Router();
$router->route('GET', '/{path:category|tag/.*}', $oldSiteController);
$router->route('GET', '/{path:\d{4}/?.*}', $oldSiteController);
$router->route('GET', '/{path:about}', $aboutPageController);
$router->route('GET', '/{page:me|projects|blog}/?', new PageController($twig, $blogFeed));
$router->route('GET', '/', new HomePageController($twig, $homeFeed));

/**
 * @todo fetch data for root and hosts from env
 * @var Root
 */
$rootDir = new Root(__DIR__.'/web');
$rootDir->setOption('mimeFile', __DIR__.'/vendor/amphp/aerys/etc/mime');


if (file_exists('/var/www/html/cert/fullchain.cer')) {
    $securehost = new Host();
    $securehost
        ->name('strayobject.co.uk')
        ->expose('*', 8443)
        ->encrypt(
            '/var/www/html/cert/fullchain.cer',
            '/var/www/html/cert/strayobject.co.uk.key',
            [
                'ciphers' => 'ECDH+AESGCM:DH+AESGCM:ECDH+AES256:DH+AES256:ECDH+AES128:DH+AES:RSA+AESGCM:RSA+AES:!aNULL:!MD5:!DSS',
                'crypto_method' => 'tlsv1.2',
            ]
        )
        ->use(new Http1Driver())
        ->use($router)
        ->use($rootDir)
    ;
}

$host = new Host();
$host
    ->name('strayobject.co.uk')
    ->expose('*', 8080)
    ->use(new Http1Driver())
    ->use($router)
    ->use($rootDir)
;

$devhost = new Host();
$devhost
    ->name('dev.strayobject.co.uk')
    ->expose('*', 7000)
    ->use(new Http1Driver())
    ->use($router)
    ->use($rootDir)
;
