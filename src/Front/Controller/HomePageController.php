<?php
declare(strict_types=1);

namespace Front\Controller;

use Aerys\{Response, Request};
use Front\Feed\UnifiedFeed;

class HomePageController
{
    private $twig;
    private $feed;

    public function __construct(\Twig_Environment $twig, UnifiedFeed $unifiedFeed)
    {
        $this->twig = $twig;
        $this->feed = $unifiedFeed;
    }

    public function __invoke(Request $request, Response $response, array $args): \Generator
    {
        $data['title'] = '';
        $data['feed'] = yield from $this->feed->__invoke();

        $content = $this->twig->render('pages/home.html', $data);
        $response->end($content);
    }
}
