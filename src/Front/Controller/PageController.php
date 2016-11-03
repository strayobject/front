<?php
declare(strict_types=1);

namespace Front\Controller;

use Aerys\{Response, Request};
use Front\Feed\UnifiedFeed;
use Amp\{function all, function once, function some, Deferred};


class PageController
{
    private $twig;
    private $blogFeed;

    public function __construct(\Twig_Environment $twig, UnifiedFeed $blogFeed)
    {
        $this->twig = $twig;
        $this->blogFeed = $blogFeed;
    }

    public function __invoke(Request $request, Response $response, array $args): \Generator
    {
        $data['title'] = $args['page'];

        /**
         * @todo replace with a middleware & combine PageController+HomePageController
         */
        if ($args['page'] == 'blog') {
            $data['feed'] = yield from $this->blogFeed->__invoke();
        }

        $response->end(
            $this->twig->render(sprintf('pages/%s.html', $args['page']), $data)
        );
    }

}
